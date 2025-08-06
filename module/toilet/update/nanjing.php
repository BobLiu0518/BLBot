<?php

loadModule('toilet.update.request');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['nanjing'] = [];
$citiesMeta['nanjing'] = [
    'name' => '南京地铁',
    'support' => true,
    'source' => '与宁同行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#FF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_nanjing.svg',
];

$cityId = '3201';
$lines = json_decode(request($cityId, 'bas/smartstation/v1/bas/line/list', ['service_id' => '01']), true)['result'];
foreach($lines as $line) {
    $stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 200, 'line_no' => $line['line_no'], 'service_id' => '01']), true)['result']['rows'];
    foreach($stations as $station) {
        if(!array_key_exists($station['station_name'], $toiletInfo['nanjing'])) {
            $toiletInfo['nanjing'][$station['station_name']] = ['toilets' => []];
        }
        $stationInfo = json_decode(request($cityId, 'bas/smartstation/v2/bas/station/detail', ['station_no' => $station['station_no'], 'line_no' => $line['line_no'], 'service_id' => '01', 'train_plan_type' => '01,02,03', 'calendar_type' => '1,6']), true)['result'];
        foreach($stationInfo['device_list'] as $facility) {
            if($facility['device_name'] == '卫生间') {
                foreach(preg_split('/\r|\n/', $facility['description']) as $toilet) {
                    $toiletInfo['nanjing'][$station['station_name']]['toilets'][] = [
                        'title' => $line['line_name'],
                        'content' => $toilet,
                    ];
                }
            }
        }
    }
}

// Station name aliases
foreach(array_keys($toiletInfo['nanjing']) as $stationName) {
    if(preg_match('/·/u', $stationName)) {
        foreach(explode('·', $stationName) as $subName) {
            if(!array_key_exists($subName, $toiletInfo['nanjing'])) {
                $toiletInfo['nanjing'][$subName] = [];
            }
            $toiletInfo['nanjing'][$subName]['redirect'] = [$stationName];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['nanjing']).' 条数据');
