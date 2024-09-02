<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['hefei'] = [];
$citiesMeta['hefei'] = [
    'name' => '合肥轨道',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#D00001',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_hefei.svg',
];

// Get stations
$cityId = '3401';
$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['service_id' => '01', 'page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
foreach($stations as $station) {
    $toiletInfo['hefei'][$station['station_name']] = [];
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['hefei']).' 条数据');