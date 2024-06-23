<?php

requireLvl(1);
use zjkal\ChinaHoliday;

$search = nextArg();
if(!$search) replyAndLeave('不知道你要查询什么呢…（可以查询金山铁路车次或车站）');
if(preg_match('/^S?(\d+)$/i', $search, $match)){
	$code = 'S'.$match[1];
	$trains = json_decode(getData('jsr/train.json'), true);
	if(!$trains[$code]) replyAndLeave('［金山铁路］车次 '.$code.' 不存在或已停开…');
	$train = $trains[$code];
	$reply = '［金山铁路］'.$train['code'].'次';
	$reply .= "\n  ".$train['from'].'→'.$train['to'].' '.preg_replace('/ \(.+\)$/', '', $train['type']);
	if($train['dates'] == 'weekdays'){
		$reply .= "\n* 仅工作日开行";
	}else if($train['dates'] == 'weekends'){
		$reply .= "\n* 仅双休日开行";
	}
	foreach($train['stations'] as $station){
		$reply .= "\n".$station['station_name'];
		for($i = 0; $i < 4 - mb_strlen($station['station_name']); $i++) $reply .= '　';
		$reply .= ' '.$station['arrive_time'].'到 '.$station['start_time'].'发';
	}
	replyAndLeave($reply);
}else{
	$stations = json_decode(getData('jsr/station.json'), true);
	$station = preg_replace('/站$/', '', $search);
	if(!$stations[$station]) replyAndLeave('［金山铁路］车站 '.$station." 不存在…\n可选车站：".implode(' ', array_keys($stations)));
	$trains = $stations[$station];

	$time = '';
	while($nextArg = nextArg()) $time .= $nextArg.' ';
	$time = $time ? strtotime($time) : (time() - 5 * 60);
	$isWorkday = ChinaHoliday::isWorkday($time);
	$trains = array_filter($trains, fn($train) =>
		($train['dates'] == 'all' || $isWorkday && $train['dates'] == 'weekdays' || !$isWorkday && $train['dates'] == 'weekends'));
	foreach($trains as $i => $train){
		if(strnatcmp(date('H:i', $time), $train['time']) < 0) break;
	}
	$result = array_splice($trains, $i, 10);
	$reply = '［金山铁路］'.$station.'站 '.date('n月j日', $time)."\n  ".date('H:i', $time).' 起最近 10 次列车：';
	foreach($result as $train){
		$reply .= "\n".$train['time'].' '.$train['code'].' 往'.$train['to'].' '.$train['type'];
	}
	replyAndLeave($reply);
}
