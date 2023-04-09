<?php

function poolSortByTime($a, $b){
	return $a['opEndTime'] - $b['opEndTime'];
}

$poolData = json_decode(getData('ark/pool.json'), true);
usort($poolData, 'poolSortByTime');

$date = nextArg();
if($date){
	$timestamp = strtotime($date);
}else{
	$timestamp = time();
}

$reply = '';
$date = date('Y/m/d', $timestamp);

foreach($poolData as $pool){
	if($pool['opEndTime'] > strval(date('Ymd', $timestamp))){
		break;
	}else if($pool['opEndTime'] >= strval(date('Ymd', $timestamp - 86400 * 13))){
		$reply .= "\n".$pool['name'].'【'.implode($pool['operators']['6']['up'], ' ').'】';
	}
}

if(!$reply){
	replyAndLeave('在 '.$date.' 没有找到卡池');
}else{
	replyAndLeave($date.' 开放的卡池信息：'.$reply);
}

?>
