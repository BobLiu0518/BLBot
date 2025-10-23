<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['wuxi'] = [];
$citiesMeta['wuxi'] = [
    'name' => '无锡地铁',
    'support' => true,
    'source' => '码上行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#FC0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_wuxi.svg',
];

// Get stations
$cityId = '3202';
// $stations = json_decode(request($cityId, 'bas/dict/v1/query-stations-lines', ['page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['station_name' => '', 'page_no' => 1, 'page_size' => 2000]), true)['result']['rows'];
foreach($stations as $station) {
    if(!preg_match('/^(无锡.+站|.+高铁站)$/', $station['station_name'])) {
        $station['station_name'] = preg_replace('/站$/', '', $station['station_name']);
    }
    if(array_key_exists($station['station_name'], $toiletInfo['wuxi'])) continue;
    $toiletInfo['wuxi'][$station['station_name']] = ['toilets' => []];
    $stationInfo = json_decode(request($cityId, 'bas/smartstation/v2/bas/station/detail', ['station_no' => explode(',', $station['station_no'])[0], 'service_id' => '01', 'train_plan_type' => '01,02,03', 'calendar_type' => '1']), true)['result'];
    foreach($stationInfo['device_list'] as $facility) {
        if(preg_match('/^(洗手间|(普通)?卫生间)$/', $facility['device_name'])) {
            foreach(explode("\n", $facility['description']) as $toilet) {
                if($toilet) {
                    $toiletInfo['wuxi'][$station['station_name']]['toilets'][] = [
                        'title' => '洗手间',
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
replyAndLeave('更新数据成功，共 '.count($toiletInfo['wuxi']).' 条数据');