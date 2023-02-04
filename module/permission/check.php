<?php

global $Event;

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}
$QQ = $QQ??$Event['user_id'];

$list = json_decode(getData('usertype.json'),true);
foreach($list as $type => $users)
	foreach($users as $user)
		if($user == $QQ)
			leave($QQ.' 的权限为 '.$type.'！');
leave($QQ.' 的权限为 User！');

?>
