<?php

function poolSortByTime($a, $b){
	return $a['time'] - $b['time'];
}

$poolData = json_decode(getData('ark/pool.json'), true);
usort($poolData, 'poolSortByTime');

$date = nextArg();
if($date){
	$timestamp = strtotime($date) + 86400 * 13;
}else{
	$timestamp = time();
}

$reply = '';
$dateRange = date('Y/m/d', $timestamp - 86400 * 13).'~'.date('Y/m/d', $timestamp);
foreach($poolData as $pool){
	if($pool['time'] > strval(date('Ymd', $timestamp))){
		break;
	}else if($pool['time'] >= strval(date('Ymd', $timestamp - 86400 * 13))){
		$reply .= "\n".$pool['name'].'【'.implode($pool['operators']['6']['up'], ' ').'】';
	}
}

if(!$reply){
	replyAndLeave('在 '.$dateRange.' 没有找到卡池');
}else{
	replyAndLeave($dateRange.' 的卡池信息：'.$reply);
}

?>
