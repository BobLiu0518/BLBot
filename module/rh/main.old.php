<?php

loadModule('rh.new');
leave();

/*
setData('rh/'.$Event['group_id'], '{"status": "initializing"}');

//不知道为什么就是想写函数
function re(string $str){
	global $Event, $CQ;
	return $CQ->sendGroupMsg($Event['group_id'], $str);
}
function le(string $str, bool $cd = true){
	global $Event;
	// delData('rh/'.$Event['group_id']);
	if($cd){
		coolDown("rh/user/{$Event['user_id']}", 10*60);
		coolDown("rh/group/{$Event['group_id']}", 10*60);
	}
	leave($str);
}
function randString(array $strArr){
	return $strArr[rand(0, sizeof($strArr)-1)];
}
function getChar(int $num){
	$result = '';
	for($n = 0; $n < $num; $n++){
		$result .= iconv('UCS-2BE', 'UTF-8', pack('H4', dechex(rand(19968, 40896))));
	}
	return $result;
}
function emojiReplace(string $str){
	return preg_replace('\[CQ:emoji,id=\d*?\]', '啊', preg_replace('\[CQ:face,id=\d*?\]', '哦', $str));
}

//基本逻辑判断
global $Event, $CQ, $Config, $Command;

// replyAndLeave('赛马场疫情防控指挥部温馨提醒您：疫情期间关爱自己关爱他人，保持社交间距，非必要不赛马。');
le('新赛马场入驻了！（装修中）', false);
if(!fromGroup())replyAndLeave('打算单人赛马嘛？');
date_default_timezone_set("Asia/Shanghai");
if(date('H') < 5 || date('H') > 22){
	replyAndLeave('赛马场不在营业时间，关门休息啦…');
}
if(date('w') != '3' && date('w') != '6' && date('w') != '0'){
	replyAndLeave('疫情期间，赛马场逢周三、周六日运营哦～');
}
$g = $Event['group_id'];
if(coolDown("rh/group/{$Event['group_id']}")<0){
	$time = -coolDown("rh/group/{$Event['group_id']}");
	replyAndLeave('赛马场清理中，大约还需要'.(((intval($time/60)>0)?(intval($time/60).'分'):'')).((($time%60)>0)?($time%60).'秒':'钟').'～');
}
if(coolDown("rh/user/{$Event['user_id']}")<0){
	$time = -coolDown("rh/user/{$Event['user_id']}");
	replyAndLeave('你的马正在休息，大约还需要'.(((intval($time/60)>0)?(intval($time/60).'分'):'')).((($time%60)>0)?($time%60).'秒':'钟').'～');
}

//发起游戏，写文件
$h = "🐴"; //[CQ:emoji,id=128052]
$nh = "🦄"; //[CQ:emoji,id=129412]
$dh = "👻";
$num = ["０", "１", "２", "３", "４", "５", "６", "７", "８", "９"];
if($nextArg = nextArg()){
	$h = $nextArg;
	if($nextArg = nextArg()){
		$nh  = $nextArg;
	}
	if(strpos($h, '[CQ:image,') !== false || strpos($nh, '[CQ:image,') !== false){
		le('不许赛图！（义正辞严）', false);
	}
	if(mb_strlen(emojiReplace($h)) > 2 || mb_strlen(emojiReplace($nh)) > 2){
		le('名字太长了 Bot 会受不了的呜呜', false);
	}
}
$f = json_decode(getData('rh/'.$g), true);
if($f){
    if($f['status'] != "banned"){
        loadModule('rh.join');
        leave();
    }else{
        replyAndLeave("管理员关停了本群内赛马场…");
    }
}
loadModule('rh.tools');
loadModule('credit.tools');
requireLvl(3);
setData('rh/'.$g, json_encode(array('status' => 'starting', 'players' => array($Event['user_id']))));

re('[CQ:reply,id='.$Event['message_id'].']已发起赛'.$h."，发送“赛马”即可加入～\n赛".$h.'将于一分钟后开始哦～'."\n赛马场疫情防控指挥部温馨提醒您：\n疫情期间关爱自己关爱他人，务必为自身和马做好防控措施，观看比赛时间隔入座，谢谢配合。");
sleep(30);
re('还有30秒赛'.$h.'开始～');
sleep(20);
re('还有10秒赛'.$h.'开始～');
sleep(10);

//开始游戏
$f = json_decode(getData('rh/'.$g),true);
setData('rh/'.$g, json_encode(array('status' => 'started', 'time' => time())));
$players = $f['players'];
$playersCount = count($players);
if($playersCount < 2)
	le('你'.$h.'的，场上只有一匹'.$h.'，没法赛'.$h.'了呢', false);

coolDown("rh/user/{$Event['user_id']}", 10*60);
//分配马
$horses = array();
foreach($players as $n => $player)
	$reply .= "[CQ:at,qq=".$player."]，你".$h."的编号为".($n+1)."～\n";
re(rtrim($reply));

for($n = 0; $n < $playersCount; $n++)
	$horses[] = new Horse(13, 16, $h, $nh, $dh);

sleep(1);
while(true){ //其实我觉得这里分开几个函数写会比较容易…
	$n = rand(0, ($playersCount-1));
	if($horses[$n]->isDead()){
		if(!rand(0,5)){ // 复活
			$horses[$n]->makeAlive();
			$reply = ($n+1).'号'.($horses[$n]->isNb()?$nh:$h).randString(array("重生了","被冥土追魂救活了","被xxs气活了"));
			foreach($horses as $n => $horse)
				$reply .= "\n".$num[$n + 1].'|'.$horse->display().'|';
			re($reply);
			sleep(5);
			if($horses[$n]->isWin()){
				$money = rand($playersCount*250, $playersCount*750);
				if(rand(0,10)){
					addCredit($players[$n], $money);
					le(($n+1).'号'.$h.'已经抵达终点了，[CQ:at,qq='.$players[$n].'] 获胜，获得'.$money.'金币哦～🏆');
				}else{
					le(($n+1).'号'.$h.'已经抵达终点了，[CQ:at,qq='.$players[$n].'] 获胜，但是'.$h.'把金币吃掉了～🏆');
				}
			}
		}else if(!$horses[$n]->isDisappeared() && !rand(0, 6)){ // 诈尸
			$horses[$n]->goAhead(1);
			$reply = ($n+1).'号'.$dh.getChar(rand(5,10));
			foreach($horses as $n => $horse)
				$reply .= "\n".$num[$n + 1].'|'.$horse->display().'|';
			re($reply);
			sleep(5);
			if($horses[$n]->isFinished()){
				le(($n+1).'号'.$dh.getChar(7).'，[CQ:at,qq='.$players[$n].'] '.getChar(rand(8, 15)));
			}
		}
		continue;
	}else{
		switch(rand(1, 13)){ //随机触发事件！这里可以随便加，但是要注意保持平衡
			case 1: case 2: case 3: case 4: case 5:
			$horses[$n]->goAhead(rand(1,2));
			$reply = randString(array('跨越了自己的一小步，'.$h.'类的一大步','太无聊了于是走了一步','不情愿的挪了一下','正在冲灯，突然发现前面有个探头，急刹车了','装了5km/h的电子限速，跑不快','在路上慢慢摇，跑不快','克服空气阻力做功，功率为μ𝑚𝑔𝑣','将体内½𝑚𝑣²的化学能转化为动能','围绕赛'.$h.'场作匀速圆周运动，摩擦力≈𝑚𝑣²/𝑟'));
			break;

			case 6: case 7:case 8:
			$horses[$n]->goAhead(rand(3,5));
			$reply = randString(array('跑了一大步','开挂了','说自己没有开挂','吃了太多华莱士，喷射了一大步','卷起来了','在泥头车前斜穿猛跑','开了加速器','执行快'.$h.'交路，越行了中间的10个甚至9个站','金球附体 ，一骑绝尘'));
			break;

			case 9: case 10:
			$horses[$n]->goBack(1);
			$reply = randString(array('受伤了，后退了一步','感到一阵眩晕','迷路了','喝了一口昏睡红茶','跑去签到了','、母いなくで寂しい','摆烂了','躺平了','去清理赛马场了','遵循了路口30码的规定，停了一下下','停下来围观事故现场','穿上了背带裤原地打起了篮球'));
			break;

			case 11: case 12:
			$horses[$n]->kill();
			if(rand(0,1)){
				if(rand(0,1))
					$reply = randString(array('吃了老八秘制小汉堡','被风控了','被群主禁言了','被烧烤店做成烤肉了','被泥头车创死力','被xxs气死了','想起来自己是陈睿的'.$h,'被💰诱惑到了，它所热爱的就是它的生活','被一个乱冲的🐮撞出场地','看到了赛马娘，爽死了','去吃烧烤，然后被烧死了'));
				else if(rand(0,1)){
					$weekday = ['日', '一', '二', '三', '四', '五', '六'];
					$horses[$n]->kill(true);
					$reply = randString(array('被丁真骑走了','被套马杆套走了','被陈睿偷走了','红了，被拉去复核核酸了','的，你'.$h.'去哪了？','进入了异世界','发生事故被拖走了','被疯狂星期'.$weekday[intval(date('w'))].'吸引了，跑出了🐴场'));
				}else{
					$horses[$n]->goAhead(20);
					$horses[$n]->kill(false);
					$reply = randString(array('被泥头车撞飞到终点，但是他寄了','开挂飞到终点然后被封号了','被华法琳血怒了，但是流血致死','以100km/h的速度撞上了电线杆','失控冲出了赛马场','被先辈撅飞了十米甚至九米'));
				}
			}else{
				$nOther = rand(0, $playersCount - 2);
				if($nOther >= $n) $nOther ++;
				$hOther = $horses[$nOther]->isNb()?$nh:$h;
				if($horses[$nOther]->isDead()){
					$horses[$nOther]->makeAlive();
					$reply = randString(array('被'.($nOther+1).'号'.$hOther.'占据了身体', '被'.($nOther+1).'号'.$hOther.'夺舍了'));
				}else{
					$reply = randString(array('被'.($nOther+1).'号'.$hOther.'踢翻了','被'.($nOther+1).'号'.$hOther.'撅死力','试图撅'.($nOther+1).'号'.$hOther.'被一转攻势撅死力','被'.($nOther+1).'号'.$hOther.'超市了','右转必停被'.($nOther+1).'号'.$hOther.'追尾了','拍车被'.($nOther+1).'号'.$hOther.'治了'));
				}
			}
			break;

			case 13:
			if($horses[$n]->isNb()){
				$horses[$n]->sbIfy();
				$reply = randString(array('限定皮肤到期了','正在随地大小变'));
			}else{
				$horses[$n]->nbIfy();
				$reply = randString(array('穿上了女装','正在随地大小变','变成了赛马娘'));
			}
			break;
		}
		$reply = ($n+1).'号'.($horses[$n]->isNb()?$nh:$h).$reply;
	}
	//展示战绩，顺便判断游戏结束了没
	$alive = false;
	foreach($horses as $n => $horse){
		if(!$horse->isDead()) //判断是不是死光了
			$alive = true;
		if($horse->isWin()) //判断有没有赢的
			$win = $n+1;
		$reply .= "\n".$num[$n + 1].'|'.$horse->display().'|';
	}
	re($reply);
	if($win){
		$money = rand($playersCount*250, $playersCount*750);
		sleep(5);
		if(rand(0, 10)){
			addCredit($players[$win-1], $money);
			le($win.'号'.$h.'成功抵达终点，[CQ:at,qq='.$players[$win-1].'] 获胜，获得'.$money.'金币哦～🏆');
		}else
			le($win.'号'.$h.'成功抵达终点，[CQ:at,qq='.$players[$win-1].'] 获胜，但是'.$h.'把金币吃掉了～🏆');
	}
	if(!$alive)
		le($h.randString(array('死光了…','无生还…','全寄了…')));
	sleep(5);
}

*/

?>
