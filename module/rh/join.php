<?php

global $Event;
use kjBot\SDK\CQCode;

$QQ = nextArg();

$g = $Event['group_id'];
$f = json_decode(getData('rh/'.$g),true);
if($f['status'] == 'started')leave("游戏正在进行中！");
else if(!$f){
	loadModule('rh');
	leave();
};

loadModule('credit.tools');

$u = $Event['user_id'];
if(isSeniorAdmin() && $a = nextArg()) {
	$u = $a;/*
	if(!(preg_match('/\d+/', $u, $match) && $match[0] == $u))
		$u = parseQQ($u);
*/}

if(in_array($u, $f['players']))leave('你已经加入游戏，不能重复添加！');
$f['players'][] = $u;
if(count($f['players'])>25)leave("你妈的，别吧");
setData('rh/'.$g, json_encode($f));
decCredit($Event['user_id'], 500);
leave('加入游戏成功，扣除500金币！当前人数：'.count($f['players']));

?>
