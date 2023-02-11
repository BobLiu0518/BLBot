<?php

// 离开赛马
function le(string $str, bool $cd = true, bool $reply = false){
	global $Event;
	delData('rh/'.$Event['group_id']);
	if($cd){
		coolDown("rh/group/".$Event['group_id'], 5*60);
	}
    if($reply){
        replyAndLeave($str);
    }else{
        leave($str);
    }
}

// 实时发送消息
function re(string $str){
	global $Event, $CQ;
	return $CQ->sendGroupMsg($Event['group_id'], $str);
}

// 触发事件后的回复
function reEvent($target, $copywriting){
    global $assets, $horses;
    $reply = '';
    if($target !== null){
        $reply .= ($target + 1).'号'.$horses[$target]->getChar();
    }
    $reply .= (gettype($copywriting) == 'array') ? randomChoose($copywriting) : $copywriting;
    foreach($horses as $n => $horse){
        $reply .= "\n".$assets['num'][$n + 1].'|'.$horse->display().'|';
    }
    re($reply);
}

// 随机选字符串
function randomChoose($var){
	return $var[array_rand($var, 1)];
}

// 随机乱码中文
function getRandChar(int $num){
	$result = '';
	for($n = 0; $n < $num; $n++){
		$result .= iconv('UCS-2BE', 'UTF-8', pack('H4', dechex(rand(19968, 40896))));
	}
	return $result;
}

// 检查自定义马
function legalCharCheck(string $str){
	return mb_strlen(preg_replace('/\\[CQ:(?:emoji|face),id=\\d*?\\]/', '啊', $str)) === 1;
}

// 初始化游戏
function initGame(){
    global $Event;

    requireLvl(3, '发起赛马', '等待其他群成员发起赛马后加入');
    setData('rh/'.$Event['group_id'], json_encode(['status' => 'initializing']));

    global $assets;
    $assets['h'] = "🐴"; //[CQ:emoji,id=128052]
    $assets['nh'] = "🦄"; //[CQ:emoji,id=129412]
    $assets['dh'] = "👻";
    $assets['num'] = ["０", "１", "２", "３", "４", "５", "６", "７", "８", "９"];

    // 检查时间
    date_default_timezone_set("Asia/Shanghai");
    if(date('H') < 5 || date('H') > 22){
        le('赛马场不在营业时间，关门休息啦…', false, true);
    }
    if(date('w') != '3' && date('w') != '6' && date('w') != '0'){
        le('新赛马场逢周三、周六日运营哦～', false, true);
    }

    // 检查cd
    if(coolDown("rh/group/".$Event['group_id']) < 0){
        $time = -coolDown("rh/group/".$Event['group_id']);
        le('赛马场清理中，大约还需要'.(((intval($time/60)>0)?(intval($time/60).'分'):'')).((($time%60)>0)?($time%60).'秒':'钟').'～', false, true);
    }
    if(coolDown("rh/user/".$Event['user_id']) < 0){
        $time = -coolDown("rh/user/".$Event['user_id']);
        le('你的马正在休息，大约还需要'.(((intval($time/60)>0)?(intval($time/60).'分'):'')).((($time%60)>0)?($time%60).'秒':'钟').'～', false, true);
    }

	// 50% 概率出现奇怪的马
	$determination = rand(1, 100);
	if($determination <= 50){
		$specialHorses = [
			['🐥', '🐣'],
			['🚶', '🏃'],
			['🧑‍🦽', '🧑‍🦼'],
			['🚙', '🚗'],
			['🚈', '🚄'],
			['🛒', '🛺'],
			['📱', '💻'],
			['👻', '👻'],
			['🏊', '🤽'],
			['🦵', '🦿'],
			['🤪', '🤩'],
			['[CQ:face,id=339]', '[CQ:face,id=337]'], /* [舔屏] [花朵脸] */
			['[CQ:face,id=63]', '[CQ:face,id=64]'], /* [玫瑰] [凋谢] */
			['[CQ:face,id=277]', '[CQ:face,id=317]'], /* [汪汪] [菜汪] */
		];
		$randHorse = randomChoose($specialHorses);
		$assets['h'] = $randHorse[0];
		$assets['nh'] = $randHorse[1];
	}

    // 识别+检查自定义马
    if($nextArg = nextArg()){
        $assets['h'] = $nextArg;
        if($nextArg = nextArg()){
            $assets['nh'] = $nextArg;
        }
        if(!legalCharCheck($assets['h'])){
            le("不能赛".$assets['h']."哦…\n(自定义马只支持单个字符或表情)", false, true);
        }
        if(!legalCharCheck($assets['nh'])){
            le("不能赛".$assets['nh']."哦…\n(自定义马只支持单个字符或表情)", false, true);
        }
    }

    setData('rh/'.$Event['group_id'], json_encode(['status' => 'starting', 'players' => [$Event['user_id']], 'horse' => $assets['h']]));

    re('[CQ:reply,id='.$Event['message_id'].']已发起赛'.$assets['h']."，发送“赛马”即可加入～\n赛".$assets['h']."将于一分钟后开始哦～");
    countDownGame(0);
}

// 参与游戏
function joinGame(){
    // 不用 le()，因为 le() 会清除群赛马数据

    global $Event;
    loadModule('credit.tools');
    requireLvl(1, '加入赛马');

    // 检查赛马场
    $rhData = json_decode(getData('rh/'.$Event['group_id']), true);
	$horse = $rhData['horse'];
    if(in_array($Event['user_id'], $rhData['players'])){
        replyAndLeave('你的'.$horse.'已经加入赛场咯～', false);
    }
    if(count($rhData['players']) >= 8){
        replyAndLeave($horse."太多了，赛".$horse."场要被塞爆了…");
    }

    // 检查cd
    if(coolDown("rh/user/".$Event['user_id']) < 0){
        $time = -coolDown("rh/user/".$Event['user_id']);
        replyAndLeave('你的'.$horse.'正在休息，大约还需要'.(((intval($time/60)>0)?(intval($time/60).'分'):'')).((($time%60)>0)?($time%60).'秒':'钟').'～');
    }

    decCredit($Event['user_id'], 1000);
    coolDown("rh/user/".$Event['user_id'], 5*60);

    $rhData['players'][] = $Event['user_id'];
    setData('rh/'.$Event['group_id'], json_encode($rhData));
    replyAndLeave('加入赛'.$horse."成功，消耗了1000金币～\n现在赛".$horse."场有".count($rhData['players'])."匹".$horse."了～");
}

// 开始前的倒计时
function countDownGame($time){
    global $Event, $assets;

    // 倒计时一分钟
    sleep(30);
    re('还有30秒赛'.$assets['h'].'开始～');
    sleep(20);
    re('还有10秒赛'.$assets['h'].'开始～');
    sleep(10);

    // 看看人数够不够
    $rhData = json_decode(getData('rh/'.$Event['group_id']), true);
    if($time === 0 && count($rhData['players']) <= 2){
        // 延迟一分钟
        re('参与赛'.$assets['h'].'的人数太少了，本场赛'.$assets['h'].'延迟一分钟开始～还有60秒～');
        countDownGame(1);
		return;
    }else if($time !== 0 && count($rhData['players']) <= 1){
		le('你'.$assets['h'].'的，场上还是只有一匹'.$assets['h'].'，没法赛'.$assets['h'].'了呢', false);
	}else{
		startGame();
	}
}

// 开始游戏
function startGame(){
    loadModule('rh.tools');
    loadModule('credit.tools');

    global $Event, $assets;

    $rhData = json_decode(getData('rh/'.$Event['group_id']), true);
    setData('rh/'.$Event['group_id'], json_encode(['status' => 'started', 'time' => time()]));
    coolDown("rh/user/".$Event['user_id'], 5*60);

    global $horses;
    $players = $rhData['players'];
    $playersCount = count($players);
    $horses = [];
    $deadHorse = [];
    $aliveHorse = range(0, $playersCount - 1);
    $reply = '';

    foreach($players as $n => $player){
        $reply .= "[CQ:at,qq=".$player."]，你".$assets['h']."的编号为".($n + 1)."～\n";
        $horses[] = new Horse(13, 16, $assets['h'], $assets['nh'], $assets['dh']);
    }
    re(rtrim($reply));

    while(true){
        // 随机触发事件
        $determination = rand(1, 100);
        $corpseFraudulent = null;
		$specialEventTriggered = false;
        if(count($deadHorse) && $determination <= 10){
            // 死马事件 10%
            $determination = rand(1, 100);
            $target = randomChoose($deadHorse);
            if($horses[$target]->isDisappeared() || $determination <= 50){
                // 复活 50%（消失马 100%）
                $horses[$target]->makeAlive();
                unset($deadHorse[$target]);
                $aliveHorse[$target] = $target;
                reEvent($target, [
                    "重生了",
                    "被冥土追魂救活了",
                    "被xxs气活了",
					"使用不死图腾复活了"
                ]);
            }else{
                // 诈尸 50%（消失马 0%）
                $horses[$target]->goAhead(1);
                $corpseFraudulent = $target;
                reEvent($target, getRandChar(rand(0, 2))."诈".getRandChar(rand(1, 2))."尸".getRandChar(rand(0, 2))."了");
            }
        }else{
            // 活马事件 90%
            $determination = rand(1, 1000);
            $target = randomChoose($aliveHorse);
            if($determination <= 400){
                // 走一小步 40%
                $horses[$target]->goAhead(rand(1, 2));
                reEvent($target, [
                    '跨越了自己的一小步，'.$assets['h'].'类的一大步',
                    '不情愿的挪了一下',
                    '正在冲灯，突然发现前面有个探头，急刹车了',
                    '装了25km/h的电子限速，跑不快',
                    '在路上慢慢摇，跑不快',
                    '克服空气阻力做功，功率为μ𝑚𝑔𝑣',
                    '围绕赛'.$assets['h'].'场作匀速圆周运动，摩擦力≈𝑚𝑣²/𝑟',
                    '没开满核定，摇车了'
                ]);
            }else if($determination <= 700){
                // 走一大步 30%
                $horses[$target]->goAhead(rand(3, 5));
                reEvent($target, [
                    '跑了一大步',
                    '开挂了',
                    '说自己没有开挂',
                    '吃了太多华莱士，喷射了一大步',
                    '卷起来了',
                    '在泥头车前斜穿猛跑',
                    '开了加速器',
                    '执行快'.$assets['h'].'交路，越行了中间的10个甚至9个站',
                    '千招百式在 CH₂=CH₂！',
                    '劲发江潮落，骑手求好评！'
                ]);
            }else if($determination <= 800){
                // 退一小步 10%
                $horses[$target]->goBack(1);
                reEvent($target, [
                    '被超了，幼小的心灵受到了创伤，后退了一步',
                    '迷路了',
                    '喝了一口昏睡红茶',
                    '跑开去签到了',
                    '、母いなくで寂しい',
                    '摆烂了',
                    '躺平了',
                    '去清理赛'.$assets['h'].'场了',
                    '冲灯失败开始倒车',
                    '停下来围观事故现场',
                    '为了避让大站快'.$assets['h'].'，停了一会儿',
                    '形不成形，意不在意，再去练练吧。'
                ]);
            }else if($determination <= 850){
                // 变装 5%
                if($horses[$target]->isNb()){
                    $horses[$target]->sbIfy();
                    reEvent($target, [
                        '限定皮肤到期了',
                        '正在随地大小变',
                        '卸妆了'
                    ]);
                }else{
                    $horses[$target]->nbIfy();
                    reEvent($target, [
                        '穿上了女装',
                        '正在随地大小变',
                        '变成了赛'.$assets['h'].'娘'
                    ]);
                }
            }else if($determination <= 925){
                // 自己寄了 7.5%
                $determination = rand(1, 100);
                if($determination <= 45){
                    // 自己作死 45%
                    $horses[$target]->kill(false);
                    unset($aliveHorse[$target]);
                    $deadHorse[$target] = $target;
                    reEvent($target, [
                        '吃了老八秘制小汉堡',
                        '被tx风控了',
                        '被群主禁言了',
                        '被烧烤店做成烤肉了',
                        '被泥头车创死力',
                        '被xxs气死了',
                        '想起来自己是陈睿的'.$assets['h'],
                        '被💰诱惑到了，它所热爱的就是它的生活',
                        '看到了赛'.$assets['h'].'娘，爽死了',
                        '去吃烧烤，然后被烧死了'
                    ]);
                }else if($determination <= 90){
                    // 消失了 45%
                    $horses[$target]->kill(true);
                    unset($aliveHorse[$target]);
                    $deadHorse[$target] = $target;
                    reEvent($target, [
                        '被丁真骑走了',
                        '被套'.$assets['h'].'杆套走了',
                        '被陈睿偷走了',
                        '的，你'.$assets['h'].'去哪了？',
                        '进入了异世界',
                        '发生事故被拖走了',
						'被萨卡兹枯朽吞噬者吞噬了'
                    ]);
                }else{
                    // 自己作大死 10%
                    $horses[$target]->kill(false);
					$horses[$target]->goAhead(20);
                    unset($aliveHorse[$target]);
                    $deadHorse[$target] = $target;
                    reEvent($target, [
                        '被泥头车撞飞到终点，但是寄了',
                        '开挂飞到终点然后被封号了',
                        '被华法琳血怒了，浑身充满了力量，但是流血致死',
                        '以100km/h的速度撞上了电线杆',
                        '失控冲出了赛'.$assets['h'].'场',
                        '被先辈撅飞了十米甚至九米'
                    ]);
                }
            }else if($determination <= 997 || $specialEventTriggered){
                // 被谋害 7.2% (已触发过特殊事件后为 7.5%)
                $murderer = rand(0, $playersCount - 2);
                if($murderer >= $target){
                    $murderer += 1;
                }
                if($horses[$murderer]->isDead()){
                    // 被夺舍
                    $horses[$target]->kill(true);
                    unset($aliveHorse[$target]);
                    $deadHorse[$target] = $target;
					$horses[$murderer]->makeAlive();
                    unset($deadHorse[$murderer]);
                    $aliveHorse[$murderer] = $murderer;
                    reEvent($target, [
                        '被'.($murderer + 1).'号'.$horses[$murderer]->getChar().'占据了身体',
                        '被'.($murderer + 1).'号'.$horses[$murderer]->getChar().'夺舍了'
                    ]);
                }else{
                    // 被谋杀
                    $horses[$target]->kill(false);
                    unset($aliveHorse[$target]);
                    $deadHorse[$target] = $target;
                    reEvent($target, [
                        '被'.($murderer + 1).'号'.$horses[$murderer]->getChar().'踢翻了',
                        '被'.($murderer + 1).'号'.$horses[$murderer]->getChar().'撅死力',
                        '试图撅'.($murderer + 1).'号'.$horses[$murderer]->getChar().'被一转攻势撅死力',
                        '被'.($murderer + 1).'号'.$horses[$murderer]->getChar().'超市了',
                        '右转必停被'.($murderer + 1).'号'.$horses[$murderer]->getChar().'追尾了'
                    ]);
                }
            }else{
                // 特殊事件 0.3% (已触发过特殊事件后为 0%)
				$specialEventTriggered = true;
                $determination = rand(1, 100);
                if($determination <= 40){
                    // 天降大灾 40%
                    foreach($horses as $horse){
                        if(!$horse->isDead()){
                            $horse->kill(false);
                        }
                    }
                    $deadHorse = range(0, $playersCount - 1);
                    $aliveHorse = [];
                    reEvent(null, [
                        '赛'.$assets['h'].'场突然起火',
                        '龙卷风摧毁了赛'.$assets['h'].'场',
                        '突然发生了大地震'
                    ]);
                }else{
                    // 时光倒流 60%
                    foreach($horses as $horse){
                        $horse->makeAlive();
                        $horse->sbIfy();
                        $horse->goTo(13);
                    }
                    $deadHorse = [];
                    $aliveHorse = range(0, $playersCount - 1);
                    reEvent(null, "时光倒流了！");
                }
            }
        }

        // 判定胜利/失败
        $alive = false;
        $win = null;
        foreach($horses as $n => $horse){
            if($horse->isWin() || ($n === $corpseFraudulent && $horse->isFinished())){
                $win = $n;
            }
        }

        if($win !== null){
            $money = rand($playersCount * 500, $playersCount * 2000);
            sleep(5);
            $determination = rand(1, 100);
            if(!$corpseFraudulent && $determination <= 90){
                // 获得金币 90%
                addCredit($players[$win], $money);
                le(($win + 1).'号'.$horses[$win]->getChar().'成功抵达终点，[CQ:at,qq='.$players[$win].'] 获胜，获得'.$money.'金币哦～🏆');
            }else{
                // 没金币了 10%
                le(($win + 1).'号'.$horses[$win]->getChar().'成功'.($corpseFraudulent === null ? '抵达终点' : getRandChar(4)).'，[CQ:at,qq='.$players[$win].'] 获胜，但是'.$horses[$win]->getChar().'把金币'.($corpseFraudulent === null ? '吃' : getRandChar(1)).'掉了～🏆');
            }
        }
        if(!count($aliveHorse)){
            le($assets['h'].randomChoose(['死光了…', '无生还…', '全寄了…']));
        }

        sleep(5);
    }
}

global $Event;

if(!fromGroup()){
    replyAndLeave('打算单人赛马嘛？');
}

if($rhData = getData('rh/'.$Event['group_id'])){
    $rhData = json_decode($rhData, true);
    switch($rhData['status']){
        case 'banned':
            replyAndLeave("管理员关停了本群内赛马场…");
            break;
        case 'initializing':
            replyAndLeave("赛马场正在准备中…\n如果长时间准备未就绪，请使用 #feedback 反馈问题");
            break;
        case 'starting':
            joinGame();
            break;
        case 'started':
            replyAndLeave("赛马场正在使用中～");
    }
}else{
    initGame();
}

?>
