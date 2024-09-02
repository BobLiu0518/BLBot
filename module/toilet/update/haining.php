<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['haining'] = [];
$citiesMeta['haining'] = [
    'name' => '海宁轨道',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#CF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_haining.svg',
];

// Get lines
$cityId = '3301';
$realCityId = '3304';
$lines = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/line/list', ['service_id' => '01', 'city_id' => $realCityId]), true)['result'];

// Get stations
foreach($lines as $line) {
    $stations = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 2000, 'line_no' => $line['line_no'], 'service_id' => '01', 'city_id' => $realCityId]), true)['result']['rows'];
    foreach($stations as $station) {
        $toiletInfo['haining'][$station['station_name']] = [];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['haining']).' 条数据');