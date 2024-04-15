<?php

global $Event;
requireLvl(1);
loadModule('attack.tools');

$duration = abs(intval(nextArg()));
if(!$duration) $duration = 1;
$end = date('Ymd', time() + 86400 * $duration);
$status = getStatus($Event['user_id']);
switch($status){
	case 'imprisoned':
	case 'confined':
		$currentEnd = getStatusEndTime($Event['user_id']);
		$end = date('Ymd', strtotime($currentEnd) + 86400 * $duration);
	case 'free':
		$data = getAttackData($Event['user_id']);
		$data['status'] = 'imprisoned';
		$data['end'] = $end;
		setAttackData($Event['user_id'], $data);
		replyAndLeave('成功'.($status == 'free' ? '把自己送进监狱，刑期至 ' : '延长自己的刑期至 ').getStatusEndTime($Event['user_id']).' ~');
		break;
	case 'hospitalized':
		replyAndLeave('住院的时候还是以身体为重吧。');
		break;
	case 'arknights':
	case 'genshin':
		replyAndLeave('你并不知道如何回到原来的世界…');
		break;
}

?>
