<?php

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
global $Event, $CQ;
loadModule('rh.tools');
$g = $Event['group_id'];
if(!fromGroup())leave('该功能仅能在群聊中使用！');

//发起游戏，写文件
$h = "[CQ:emoji,id=128052]";
$f = getData('rh/'.$g);
if($f)leave('游戏正在进行中，请勿重复开始！');
setData('rh/'.$g, json_encode(array('status' => 'starting', 'players' => array($Event['user_id']))));
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
if(nextArg())$players[] = 2094361499;
$playersCount = count($players);
if($playersCount < 2)
	le('人数不足，游戏结束！');

//分配马
$horses = array();
foreach($players as $n => $player)
	$reply .= "[CQ:at,qq=".$player."]，你".$h."的编号为".($n+1)."！\n";
re(rtrim($reply));

//硬性规定赛道长度的话，人多的时候不好
//根据玩家人数来分配赛道长度，人多的时候也不好
for($n = 0; $n < $playersCount; $n++)
	$horses[] = new Horse();

sleep(1);
while(true){ //其实我觉得这里分开几个函数写会比较容易…
	$n = rand(0, ($playersCount-1));
	if($horses[$n]->isDead())continue; //死马不能动！！！
	switch(rand(1, 13)){ //随机触发事件！这里可以随便加，但是要注意保持平衡
		case 1: case 2: case 3: case 4: case 5:
		$horses[$n]->goAhead(2);
		$reply = randString(array('跨越了自己的一小步，马类的一大步！','觉得过于无聊于是走了一步！','不情愿的挪了一下屁股！','被奖杯诱惑到了'));
		break;

		case 6: case 7:case 8:
		$horses[$n]->goAhead(4);
		$reply = randString(array('跑了一大步，可喜可贺！','向着闪闪发光的奖杯跑了几步！','似乎看到了功成业就，女神在向他招手！'));
		break;

		case 9: case 10:
		$horses[$n]->goBack(1);
		$reply = randString(array('照了一下镜子，被自己的样子吓到，后退了一步！','感到一阵眩晕！','迷路了！'));
		break;

		case 11:
		$horses[$n]->kill();
		$reply = randString(array('被晒死了！','吼了一声“NM$L”，随即倒在了地上！','绊了一跤，摔死了！'));
		break;

		case 12: case 13:
		if($horses[$n]->isNb()){
			$horses[$n]->sbIfy();
			$reply = '变回了一匹马！';
		}else{
			$horses[$n]->nbIfy();
			$reply = '变成了一只独角兽！';
		}
		break;
	}
	re(($n+1).'号'.$h.$reply);

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
	if($win)
		le($win.'号'.$h.'成功抵达终点，获胜！[CQ:emoji,id=127942]');
	if(!$alive)
		le($h.'死光了，没有'.$h.'获胜！');
	sleep(5);
}

?>
