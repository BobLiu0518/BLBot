<?php

global $Event;
$g = $Event['group_id'];
$f = json_decode(getData('rh/'.$g),true);
if($f['status'] == 'started')leave("游戏正在进行中！");
else if(!$f){
	loadModule('rh');
	leave();
};
if(in_array($Event['user_id'], $f['players']))leave('你已经加入游戏，不能重复添加！');
$f['players'][] = $Event['user_id'];
setData('rh/'.$g, json_encode($f));
leave('加入游戏成功！当前人数：'.count($f['players']));

?>
