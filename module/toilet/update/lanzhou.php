<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['lanzhou'] = [];
$citiesMeta['lanzhou'] = [
    'name' => '兰州轨道交通',
    'support' => true,
    'source' => '兰州轨道 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#0097D8',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_lanzhou_plain.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://oms-gateway.lzgdjt.com/manage/manage_line/list'), true)['data'];
$stationsApi = 'https://oms-gateway.lzgdjt.com/manage/manage_station/listByCache?lineNo=';
$stationDataApi = 'https://oms-gateway.lzgdjt.com/manage/manage_station/getByStationNo?stationNo=';

// Get stations
foreach($lines as $line) {
    $stations = json_decode(file_get_contents($stationsApi.$line['lineNo']), true)['data'];
    foreach($stations as $station) {
        if(!preg_match('/^.+火车站$/', $station['stationName'])) {
            $station['stationName'] = preg_replace('/站$/', '', $station['stationName']);
        }
        if(!array_key_exists($station['stationName'], $toiletInfo['lanzhou'])) {
            $toiletInfo['lanzhou'][$station['stationName']] = ['toilets' => []];
        }
        $stationData = json_decode(file_get_contents($stationDataApi.$station['stationNo']), true)['data'];
        if($stationData['toilet']) {
            $toiletInfo['lanzhou'][$station['stationName']]['toilets'][] = [
                'title' => $line['lineName'],
                'content' => $stationData['toilet'],
            ];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['lanzhou']).' 条数据');