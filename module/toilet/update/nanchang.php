<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['nanchang'] = [];
$citiesMeta['nanchang'] = [
    'name' => '南昌地铁',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#F90012',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_nanchang.svg',
];

// Get lines
$cityId = '3601';
$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 2000, 'service_id' => '01']), true)['result']['rows'];

// Get stations
foreach($stations as $station) {
    $station['station_name'] = preg_replace('/（阳光寄存点）$/u', '', $station['station_name']);
    if(!preg_match('/^南昌(火车|西)站$/u', $station['station_name'])) {
        $station['station_name'] = preg_replace('/站$/u', '', $station['station_name']);
    }
    $toiletInfo['nanchang'][$station['station_name']] = [];
}

$toiletInfo['nanchang']['大岗'] = ['redirect' => ['南昌中学']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['nanchang']).' 条数据');
