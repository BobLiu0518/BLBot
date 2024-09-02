<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['chongqing'] = [];
$citiesMeta['chongqing'] = [
    'name' => '重庆轨道交通',
    'support' => true,
    'source' => '渝畅行 App (最新版及1.0版)',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#008D30',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_chongqing.svg',
];

// Get stations data from Yuchangxing app (Version 1.0)
$cityId = '5000';
$stations = json_decode(request($cityId, 'bas/dict/v1/query-stations-lines', ['page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$toiletInfoOld = [];
foreach($stations as $station) {
    $toiletInfoOld[$station['station_name']] = ['toilets' => []];
    $stationInfo = json_decode(request($cityId, 'bas/dict/v1/get-station', ['station_id' => $station['station_id']]), true)['result'];
    foreach($stationInfo['facilities'] as $facility) {
        if($facility['fac_name'] == '卫生间') {
            foreach(explode("\n", $facility['fac_desc']) as $toilet) {
                $toiletInfoOld[$station['station_name']]['toilets'][] = [
                    'title' => '卫生间',
                    'content' => preg_replace('/^\d+，/', '', $toilet).'（旧版数据）',
                ];
            }
        }
    }
}

// Get stations data from Yuchangxing app (Latest version)
$lines = json_decode(request($cityId, 'bas/smartstation/v1/bas/line/list', ['service_id' => '01']), true)['result'];
foreach($lines as $line) {
    $stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 200, 'line_no' => $line['line_no'], 'service_id' => '01']), true)['result']['rows'];
    foreach($stations as $station) {
        if(array_key_exists($station['station_name'], $toiletInfo['chongqing'])) continue;
        $toiletInfo['chongqing'][$station['station_name']] = array_key_exists($station['station_name'], $toiletInfoOld) ? $toiletInfoOld[$station['station_name']] : ['toilets' => []];
        $stationInfo = json_decode(request($cityId, 'bas/smartstation/v2/bas/station/detail', ['station_no' => $station['station_no'], 'service_id' => '01', 'train_plan_type' => '01,02,03']), true)['result'];
        foreach($stationInfo['device_list'] as $facility) {
            if($facility['device_name'] == '卫生间') {
                foreach(explode("\n", $facility['description']) as $toilet) {
                    $toiletInfo['chongqing'][$station['station_name']]['toilets'][] = [
                        'title' => '卫生间',
                        'content' => $toilet,
                    ];
                }
            }
        }
    }
}

// Old station names
$renamedStations = [
    '平场' => '欢乐谷',
    '沙正街' => '重庆大学',
    '天生' => '西南大学',
    '五公里' => '重庆工商大学',
    '二塘' => '重庆交通大学',
];
foreach($renamedStations as $oldName => $newName) {
    $toiletInfo['chongqing'][$oldName] = ['redirect' => [$newName]];
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['chongqing']).' 条数据');