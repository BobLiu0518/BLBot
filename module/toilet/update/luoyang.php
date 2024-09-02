<?php

require('request.php');

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['luoyang'] = [];
$citiesMeta['luoyang'] = [
	'name' => '洛阳轨道交通',
	'support' => true,
	'source' => '洛易行 App',
	'time' => date('Y/m/d'),
	'color' => [
		'main' => '#C70826',
	],
	'font' => 'CN',
	'logo' => 'metro_logo_luoyang.svg',
];

// Get stations
$cityId = '4103';
$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['station_name' => '', 'page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];

// Get data
foreach($stations as $station) {
	if(!preg_match('/(火车|高铁)站$/u', $station['station_name'])) {
		$station['station_name'] = preg_replace('/站$/u', '', $station['station_name']);
	}
	$toiletInfo['luoyang'][$station['station_name']] = ['toilets' => []];
	$stationInfo = json_decode(request($cityId, 'bas/smartstation/v2/bas/station/detail', ['station_no' => explode(',', $station['station_no'])[0], 'service_id' => '01', 'train_plan_type' => '01,02,03', 'calendar_type' => '1,6']), true)['result'];
	foreach($stationInfo['device_list'] as $facility) {
		if($facility['device_name'] == '卫生间') {
			if(preg_match('/^(站[厅台])(?:卫生间)?[:：]([\s\S]+)$/u', $facility['description'], $match)) {
				foreach(preg_split("/[\n;；]/u", $match[2]) as $toilet) {
					$toiletInfo['luoyang'][$station['station_name']]['toilets'][] = [
						'title' => $match[1],
						'content' => $toilet,
					];
				}
			} else {
				$toiletInfo['luoyang'][$station['station_name']]['toilets'][] = [
					'title' => '卫生间',
					'content' => preg_replace('/\s|。/u', '', $toilet),
				];
			}
		}
	}
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['luoyang']).' 条数据');