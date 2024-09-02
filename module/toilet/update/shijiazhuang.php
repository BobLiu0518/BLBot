<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['shijiazhuang'] = [];
$citiesMeta['shijiazhuang'] = [
	'name' => '石家庄轨道交通',
	'support' => true,
	'source' => '石家庄轨道交通 App',
	'time' => date('Y/m/d'),
	'color' => [
		'main' => '#00BA00',
		'secondary' => '#00A327',
	],
	'font' => 'CN',
	'logo' => 'metro_logo_shijiazhuang.svg',
];

// Get stations
$cityId = '1301';
$stations = json_decode(request($cityId, 'bas/dict/v1/query-stations-lines', ['page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];

// Get data
foreach($stations as $station) {
	if(!preg_match('/^石家庄(东)?站$/', $station['station_name'])) {
		$station['station_name'] = preg_replace('/站$/', '', $station['station_name']);
	}
	$toiletInfo['shijiazhuang'][$station['station_name']] = ['toilets' => []];
	$stationInfo = json_decode(request($cityId, 'bas/others/v1/pis/flow/congestion/and/schedule/station', ['service_id' => '01', 'station_id' => $station['station_id']]), true)['result']['dict_station_info'];
	foreach($stationInfo['facilities'] as $facility) {
		if($facility['fac_name'] == '卫生间') {
			foreach(explode('；', $facility['fac_desc']) as $toilet) {
				if(preg_match('/^(.+)：(.+)$/', $toilet, $match)) {
					$toiletInfo['shijiazhuang'][$station['station_name']]['toilets'][] = [
						'title' => $match[1],
						'content' => $match[2],
					];
				} else {
					$toiletInfo['shijiazhuang'][$station['station_name']]['toilets'][] = [
						'title' => '卫生间',
						'content' => $toilet,
					];
				}
			}
		}
	}
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['shijiazhuang']).' 条数据');