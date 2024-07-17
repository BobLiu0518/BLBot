<?php

requireLvl(6);

// 2024/7/1~2024/12/25 期间停开车次，12306 还能查到，很莫名其妙，手动录入了
$canceledTrains = [
	'S1607', 'S1017', 'S1611', 'S1615', 'S1023', 'S1621', 'S1025', 'S1027', 'S1623', 'S1010',
	'S1012', 'S1616', 'S1014', 'S1016', 'S1018', 'S1202', 'S1620', 'S1024', 'S1026',
];

$trainListApi = 'https://search.12306.cn/search/v1/train/search';
$trainDetailApi = 'https://kyfw.12306.cn/otn/queryTrainInfo/query';

$trainData = [];
$stationData = [];
$context = stream_context_create([
	'http' => [
		'method' => 'GET',
		'header' => 'User-Agent: BLBot',
	],
]);
$weekdayTrainList = json_decode(file_get_contents($trainListApi.'?keyword=S&date='.date('Ymd', strtotime('Next Monday'))), true);
$weekendTrainList = json_decode(file_get_contents($trainListApi.'?keyword=S&date='.date('Ymd', strtotime('Next Sunday'))), true);
$trainList = [];
foreach($weekdayTrainList['data'] as $train){
	if($train['from_station'] != '上海南' && $train['to_station'] != '上海南') continue;
	$trainList[$train['station_train_code']] = [
		'code' => $train['station_train_code'],
		'train_no' => $train['train_no'],
		'dates' => 'weekdays',
		'from' => $train['from_station'],
		'to' => $train['to_station'],
		'stations_count' => $train['total_num'],
	];
}
foreach($weekendTrainList['data'] as $train){
	if($train['from_station'] != '上海南' && $train['to_station'] != '上海南') continue;
	if($trainList[$train['station_train_code']]){
		$trainList[$train['station_train_code']]['dates'] = 'all';
		continue;
	}
	$trainList[$train['station_train_code']] = [
		'code' => $train['station_train_code'],
		'train_no' => $train['train_no'],
		'dates' => 'weekends',
		'from' => $train['from_station'],
		'to' => $train['to_station'],
		'stations_count' => $train['total_num'],
	];
}

foreach($trainList as $train){
	if(in_array($train['code'], $canceledTrains)) continue;

	$trainDetail = json_decode(file_get_contents($trainDetailApi.'?leftTicketDTO.train_no='.$train['train_no'].'&leftTicketDTO.train_date='
		.date('Y-m-d', strtotime($train['dates'] == 'weekdays' ? 'Next Monday' : 'Next Sunday')).'&rand_code=', false, $context), true);

	$stations = $trainDetail['data']['data'];
	if($train['stations_count'] == 2){
		$trainType = '直达';
	}else if($train['stations_count'] == 8){
		$trainType = '站站停';
	}else if(in_array($train['from'], ['上海南', '金山卫']) && in_array($train['to'], ['上海南', '金山卫'])){
		$trainType = [];
		foreach($stations as $station){
			$trainType[] = $station['station_name'];
		}
		$trainType = '大站停 ('.implode(' ', array_splice($trainType, 1, -1)).')';
	}else{
		$trainType = [];
		foreach($stations as $station){
			$trainType[] = $station['station_name'];
		}
		$trainType = '其他 ('.implode('-', $trainType).')';
	}

	$trainData[$train['code']] = [
		'code' => $train['code'],
		'type' => $trainType,
		'dates' => $train['dates'],
		'from' => $train['from'],
		'to' => $train['to'],
		'stations' => $stations,
	];

	foreach($stations as $station){
		if(!$stationData[$station['station_name']]) $stationData[$station['station_name']] = [];
		$stationData[$station['station_name']][] = [
			'time' => $station['start_time'],
			'code' => $train['code'],
			'type' => $trainType,
			'dates' => $train['dates'],
			'from' => $train['from'],
			'to' => $train['to'],
		];
	}
}

foreach($stationData as $stationName => $stationTrains){
	usort($stationData[$stationName], function($a, $b){ return strnatcmp($a['time'], $b['time']); });
}

setData('jsr/train.json', json_encode($trainData));
setData('jsr/station.json', json_encode($stationData));

replyAndLeave('更新数据成功，共 '.count($trainData).' 车次');
