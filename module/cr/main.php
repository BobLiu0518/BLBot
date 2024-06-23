<?php

requireLvl(3);

$trainListApi = 'https://search.12306.cn/search/v1/train/search';
$trainDetailApi = 'https://kyfw.12306.cn/otn/queryTrainInfo/query';

$code = strtoupper(nextArg() ?? '');
if(!$code) replyAndLeave('不知道你想要查询什么车次呢…');
else if(!preg_match('/^(G|D|C|Z|T|K|S|Y|L|X|N)?\d+$/', $code)) replyAndLeave('这好像不是车次号的格式噢…');
$time = '';
while($nextArg = nextArg()) $time .= $nextArg.' ';
$time = $time ? strtotime($time) : time();

$context = stream_context_create([
	'http' => [
		'method' => 'GET',
		'header' => 'User-Agent: BLBot',
	],
]);
$trainList = json_decode(file_get_contents($trainListApi.'?keyword='.$code.'&date='.date('Ymd', $time)), true);
if(!count($trainList['data'] ?? [])) replyAndLeave('没有找到 '.date('n月j日', $time).' '.$code.' 次的信息…');

$train = $trainList['data'][0];
$trainDetail = json_decode(file_get_contents($trainDetailApi.'?leftTicketDTO.train_no='.$train['train_no'].'&leftTicketDTO.train_date='
	.date('Y-m-d', $time).'&rand_code=', false, $context), true);
$codes = [];
$reply = '';
$maxStationNameLength = 0;

foreach($trainDetail['data']['data'] as $station){
	$maxStationNameLength = max(mb_strlen(preg_replace('/\s/', '', $station['station_name'])), $maxStationNameLength);
}
foreach($trainDetail['data']['data'] as $n => $station){
	$station['station_name'] = preg_replace('/\s/', '', $station['station_name']);
	$reply .= "\n".$station['station_no'].' '.$station['station_name'];
	for($i = mb_strlen($station['station_name']); $i < $maxStationNameLength; $i++){
		$reply .= '　';
	}
	if($n != 0) $reply .= ' '.$station['arrive_time'].'到';
	if($n != count($trainDetail['data']['data']) - 1) $reply .= ' '.$station['start_time'].'发';
	if($station['arrive_day_diff']) $reply .= ' (+'.$station['arrive_day_diff'].')';
	if(count($codes) && $codes[count($codes) - 1] != $station['station_train_code']){
		$reply .= "\n　 (车次号变更为 ".$station['station_train_code'].')';
	}
	$codes[] = $station['station_train_code'];
}

if(!in_array($code, $codes)) replyAndLeave('没有找到 '.date('n月j日', $time).' '.$code.' 次的信息…');
$reply = implode('/', array_unique($codes)).'次 ('.$trainDetail['data']['data'][0]['train_class_name'].') '.date('n月j日', $time)
	."\n".preg_replace('/\s/', '', $train['from_station']).' → '.preg_replace('/\s/', '', $train['to_station'])
	.' 共'.$train['total_num'].'站'.$reply;

replyAndLeave($reply);
