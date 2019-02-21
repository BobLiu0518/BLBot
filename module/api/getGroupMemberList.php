<?php

global $CQ, $Queue, $Event;
requireSeniorAdmin();
if(fromGroup())$group = $Event["group_id"];
if($nextArg = nextArg())$group = $nextArg;
if(!$group)leave('请输入群号！');
$list = str_split($CQ->getGroupMemberList(intval($group)),5000);
foreach($list as $item)
	$Queue[]= sendPM($item);

?>
