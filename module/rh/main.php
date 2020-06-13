<?php

requireLvl(3);

//不知道为什么就是想写函数
function re(string $str){
	global $Event, $CQ;
	return $CQ->sendGroupMsg($Event['group_id'], $str);
}
function le(string $str){
	global $Event;
	delData('rh/'.$Event['group_id']);
	leave($str);
}
function randString(array $strArr){
	return $strArr[rand(0, sizeof($strArr)-1)];
}

//基本逻辑判断
global $Event, $CQ, $Config;
loadModule('rh.tools');
loadModule('credit.tools');
$g = $Event['group_id'];
if(!fromGroup())leave('该功能仅能在群聊中使用！');

if(coolDown("rh/{$Event['group_id']}")<0)leave('本命令每群每10分钟只能使用一次！');

//发起游戏，写文件
$h = "[CQ:emoji,id=128052]";
$nh = "🦄"; //[CQ:emoji,id=129412]
$f = getData('rh/'.$g);
if($f)leave('游戏正在进行中，请勿重复开始！');
setData('rh/'.$g, json_encode(array('status' => 'starting', 'players' => array($Event['user_id']))));

if(($def1 = nextArg()) !== NULL && ($def2 = nextArg()) !== NULL){$h = $def1; $nh = $def2;}

re('已发起赛'.$h."游戏，发送指令 #rh.join 加入！\n一分钟后游戏自动开始！");
sleep(30);
re('还有30秒赛'.$h.'游戏开始');
sleep(25);
re('还有5秒赛'.$h.'游戏开始');
sleep(5);

//开始游戏
$f = json_decode(getData('rh/'.$g),true);
setData('rh/'.$g, json_encode(array('status' => 'started')));
$players = $f['players'];
$playersCount = count($players);
if($playersCount < 2)
	le('你'.$h.'的，人数不足，游戏结束！');

//coolDown("rh/{$Event['group_id']}",10*60);

//分配马
$horses = array();
foreach($players as $n => $player)
	$reply .= "[CQ:at,qq=".$player."]，你".$h."的编号为".($n+1)."！\n";
re(rtrim($reply));

//硬性规定赛道长度的话，人多的时候不好
//根据玩家人数来分配赛道长度，人多的时候也不好
for($n = 0; $n < $playersCount; $n++)
	$horses[] = new Horse(10, 13, $h, $nh);

sleep(1);
while(true){ //其实我觉得这里分开几个函数写会比较容易…
	$n = rand(0, ($playersCount-1));
	if($horses[$n]->isDead()){
		if(!rand(0,9)){
			$horses[$n]->makeAlive();
			re(($n+1).'号'.($horses[$n]->isNb()?$nh:$h)."复活了！");
			$reply = "";
			foreach($horses as $n => $horse)
				$reply .= $horse->display();
			re(rtrim($reply));
		}
		continue;
	}
	switch(rand(1, 13)){ //随机触发事件！这里可以随便加，但是要注意保持平衡
		case 1: case 2: case 3: case 4: case 5:
		$horses[$n]->goAhead(2);
		$reply = randString(array('跨越了自己的一小步，'.$h.'类的一大步！','觉得过于无聊于是走了一步！','不情愿的挪了一下屁股！','被奖杯诱惑到了'));
		break;

		case 6: case 7:case 8:
		$horses[$n]->goAhead(4);
		$reply = randString(array('跑了一大步，可喜可贺！','向着闪闪发光的奖杯跑了几步！','开挂了！'));
		break;

		case 9: case 10:
		$horses[$n]->goBack(1);
		$reply = randString(array('照了一下镜子，被自己的样子吓到，后退了一步！','感到一阵眩晕！','迷路了！','喝了一口昏睡红茶！'));
		break;

		case 11:
		$horses[$n]->kill();
		$reply = randString(array('吃了老八秘制小汉堡！','螺旋升天了！','被群主禁言了！','吼了一声“NM$L”，随即倒在了地上！','绊了一跤，摔死了！','被SWB6129BEV38碾死了！','感染了冠状病毒！'));
		break;

		case 12: case 13:
		if($horses[$n]->isNb()){
			$horses[$n]->sbIfy();
			$reply = '限定皮肤到期了！';
		}else{
			$horses[$n]->nbIfy();
			$reply = '穿上了女装！';
		}
		break;
	}
	re(($n+1).'号'.($horses[$n]->isNb()?$nh:$h).$reply);

	//展示战绩，顺便判断游戏结束了没
	$reply = "";
	$alive = false;
	foreach($horses as $n => $horse){
		if(!$horse->isDead()) //判断是不是死光了
			$alive = true;
		if($horse->isWin()) //判断有没有赢的
			$win = $n+1;
		$reply .= $horse->display();
	}
	re(rtrim($reply));
	if($win){
		$money = rand($playersCount*250, $playersCount*750);
		addCredit($players[$win-1], $money);
		le($win.'号'.$h.'成功抵达终点，[CQ:at,qq='.$players[$win-1].'] 获胜，获得'.$money.'金币！[CQ:emoji,id=127942]');
	}
	if(!$alive)
		le($h.'死光了，没有'.$h.'获胜！');
	sleep(5);
}

?>
