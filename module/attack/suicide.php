<?php

global $Event;
requireLvl(1);
loadModule('attack.tools');

$duration = abs(intval(nextArg()));
if(!$duration) $duration = 1;
$duration = min($duration, 1e6) * 86400;
$end = date('Ymd', time() + $duration);
$status = getStatus($Event['user_id']);
switch($status){
	case 'imprisoned':
	case 'confined':
		$currentEnd = getStatusEndTime($Event['user_id']);
		if($currentEnd != '∞'){
			$end = date('Ymd', strtotime($currentEnd) + $duration);
		}else{
			$end = 99999999;
		}
	case 'free':
		$data = getAttackData($Event['user_id']);
		$data['status'] = 'imprisoned';
		$data['end'] = strval(min(intval($end), 99999999));
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
	case 'universe':
		replyAndLeave('你已经不在地球上了…');
		break;
}

?>
