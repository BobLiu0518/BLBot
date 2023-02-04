<?php

function errorHandler($a, $b, $c, $d){
    re('Error['.$a.']: '.$b.' @Line'.$d);
}

set_error_handler('errorHandler');

// 离开赛马
function le(string $str, bool $cd = true, bool $reply = false){
	return;
}

// 实时发送消息
function re(string $str){
	global $Event, $CQ;
	return $CQ->sendGroupMsg($Event['group_id'], $str);
}

// 触发事件后的回复
function reEvent($target, $copywriting){
    global $statistics;
    $statistics['steps'][count($statistics['steps']) - 1] += 1;
    return;
}

// 随机选字符串
function randomChoose($var){
	return $var[array_rand($var, 1)];
}

// 初始化游戏
function initGame(){
    requireLvl(4, '赛马实验');
    loadModule('rh.tools');

    global $statistics, $playersCount, $reply;
    $statistics = [
        "steps" => [],
        "win" => 0,
        "dead" => 0,
        "specialDead" => 0,
        "coinPool" => 0
    ];

    $playersCount = nextArg();
    if(!$playersCount){
        $reply = "未指定马数量，使用默认值：6\n";
        $playersCount = 6;
    }else{
        $reply = '马数量：'.$playersCount."\n";
        $playersCount = intval($playersCount);
    }

    $repeatTimes = nextArg();
    if(!$repeatTimes){
        $reply .= "未指定实验次数，使用默认值：100\n";
        $repeatTimes = 100;
    }else{
        $reply .= '实验次数：'.$repeatTimes."\n";
        $repeatTimes = intval($repeatTimes);
    }

    foreach(range(1, $repeatTimes) as $n){
        $statistics['steps'][] = 0;
        startGame();
    }

    re($reply.$statistics['win'].'('.(100 * $statistics['win'] / $repeatTimes).'%)局有胜利，'.$statistics['dead'].'('.(100 * $statistics['dead'] / $repeatTimes).'%)局自然死光，'.$statistics['specialDead'].'('.(100 * $statistics['specialDead'] / $repeatTimes).'%)局特殊事件死光，平均'.(array_sum($statistics['steps'])/count($statistics['steps'])).'步结束，金币池平均变化'.(($statistics['coinPool'] / $repeatTimes) > 0 ? ('+'.($statistics['coinPool'] / $repeatTimes)): ($statistics['coinPool'] / $repeatTimes)));
}

// 开始游戏
function startGame(){
    global $statistics, $playersCount;
    $horses = [];
    $deadHorse = [];
    $aliveHorse = range(0, $playersCount - 1);
    $assets = ['h' => '', 'nh' => '', 'dh' => ''];

    $statistics['coinPool'] -= ($playersCount - 1) * 1000;

    foreach(range(0, $playersCount - 1) as $n){
        $horses[] = new Horse(13, 16);
    }

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
                reEvent($target, "");
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
					'开启了技能“未照耀的荣光”',
					'开启了技能“耀阳颔首”'
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
					'为了避让大站快'.$assets['h'].'，停了一会儿'
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
                    $statistics['specialDead'] += 1;
                    return;
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
            $statistics['win'] += 1;

            $money = rand($playersCount * 500, $playersCount * 2000);
            $determination = rand(1, 100);
            if(!$corpseFraudulent && $determination <= 90){
                // 获得金币 90%
                $statistics['coinPool'] += $money;
            }else{
                // 没金币了 10%
            }
            return;
        }
        if(!count($aliveHorse)){
            $statistics['dead'] += 1;
            return;
        }
    }
}

global $Event;

initGame();

?>