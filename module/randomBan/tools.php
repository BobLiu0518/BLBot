<?php

function randomBan($maxTime = 600){
	global $Event, $CQ;
	$time = rand(1, $maxTime);
	$role = $CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'], true)->role;
	$bot = $CQ->getGroupMemberInfo($Event['group_id'], config('bot'), true)->role;
	if($role == 'owner' || ($role == 'admin' && $bot == 'member')){
		return false;
	}else if($bot == 'member'){
		return false;
	}else if($bot == 'admin' && $role == 'admin'){
		return false;
	}

	$CQ->setGroupBan($Event['group_id'], $Event['user_id'], $time);
	return $time;
}

?>
