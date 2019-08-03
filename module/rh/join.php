<?php

global $Event;
$g = $Event['group_id'];
$f = json_decode(getData('rh/'.$g),true);
if($f['status'] == 'started')leave("游戏正在进行中！");
else if(!$f){
	loadModule('rh');
	leave();
};
$u = $Event['user_id'];
if(isSeniorAdmin() && $a = nextArg())$u = $a;
if(in_array($u, $f['players']))leave('你已经加入游戏，不能重复添加！');
$f['players'][] = $u;
setData('rh/'.$g, json_encode($f));
leave('加入游戏成功！当前人数：'.count($f['players']));

?>
