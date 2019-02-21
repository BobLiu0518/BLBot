<?php

global $Queue, $Event, $Command;

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}

if(count($Command)-1 != 2)leave('参数错误！');

$permission = nextArg();
if(!in_array($permission,array("SeniorAdmin","Admin","User","Blacklist")))leave('权限仅能为 SeniorAdmin Admin User Blacklist 中的一个！');

if($permission == "SeniorAdmin" || $permission == "Admin")
	requireMaster();
else
	requireSeniorAdmin();

$list = json_decode(getData('usertype.json'),true);
foreach($list as $type => $users)
	foreach($users as $n => $user)
		if($user == $QQ)
			unset($list[$type][$n]);

if($permission != "User")
	$list[$permission][] = $QQ;
//保存
setData('usertype.json',json_encode($list));
$Queue[]= sendBack('已将 '.$QQ.' 的权限设置为 '.$permission.'！');

?>
