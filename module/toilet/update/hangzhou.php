<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['hangzhou'] = [];
$citiesMeta['hangzhou'] = [
    'name' => '杭州地铁',
    'support' => '杭港地铁车站数据缺失',
    'source' => '杭州地铁 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#EF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_hangzhou.svg',
];

// Get lines
$cityId = '3301';
$lines = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/line/list', ['service_id' => '01', 'city_id' => $cityId]), true)['result'];

// Get stations
foreach($lines as $line) {
    $stations = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 2000, 'line_no' => $line['line_no'], 'service_id' => '01', 'city_id' => $cityId]), true)['result']['rows'];
    foreach($stations as $station) {
        if(array_key_exists($station['station_name'], $toiletInfo['hangzhou'])) {
            continue;
        }
        $toiletInfo['hangzhou'][$station['station_name']] = ['toilets' => []];
        $stationInfo = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/station/detail', ['station_no' => $station['station_no'], 'service_id' => '01', 'map_type' => '01', 'train_plan_type' => '01,02,03', 'city_id' => $cityId]), true)['result'];
        foreach($stationInfo['device_list'] as $facility) {
            if($facility['device_name'] == '卫生间') {
                foreach(explode("\n", $facility['description']) as $toilet) {
                    if($toilet == '卫生间') {
                        continue;
                    }
                    $toiletInfo['hangzhou'][$station['station_name']]['toilets'][] = [
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
replyAndLeave('更新数据成功，共 '.count($toiletInfo['hangzhou']).' 条数据');