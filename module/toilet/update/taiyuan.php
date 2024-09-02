<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['taiyuan'] = [];
$citiesMeta['taiyuan'] = [
    'name' => '太原地铁',
    'support' => true,
    'source' => '听景 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#EFAC00',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_taiyuan.svg',
];

// Get stations
$stations = json_decode(file_get_contents('https://tjgateway.tymetro.ltd/manage/manage_station/listByCache'), true)['data'];
$stationDataApi = 'https://tjgateway.tymetro.ltd/manage/manage_station/getByStationNo?stationNo=';

// Get data
foreach($stations as $station) {
    if(!preg_match('/^太原(东|南|西|北)?站(.+广场)?$/', $station['stationName'])) {
        $station['stationName'] = preg_replace('/站$/', '', $station['stationName']);
    }
    $toiletInfo['taiyuan'][$station['stationName']] = ['toilets' => []];
    $stationInfo = json_decode(file_get_contents($stationDataApi.$station['stationNo']), true)['data'];
    foreach(explode(' ', str_replace('"', '', $stationInfo['toilet'])) as $toilet) {
        $toiletInfo['taiyuan'][$station['stationName']]['toilets'][] = [
            'title' => '卫生间',
            'content' => $toilet,
        ];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['taiyuan']).' 条数据');