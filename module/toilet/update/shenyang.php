<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['shenyang'] = [];
$citiesMeta['shenyang'] = [
    'name' => '沈阳地铁',
    'support' => true,
    'source' => '沈阳地铁微信公众号站点信息页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#DC000E',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_shenyang.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://www.symtc.com/sjzx/api/basic/b001'), true)['data'];
$lineMap = [];
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/json',
        'content' => json_encode(['line_id' => '00']),
    ],
]);
$stations = json_decode(file_get_contents('https://www.symtc.com/sjzx/api/basic/b002', false, $context), true)['data'];
$stationApi = 'https://www.symtc.com/sjzx/api/basic/b009';

foreach($lines as $line) {
    $citiesMeta['shenyang']['color'][$line['line_name']] = $line['line_color'];
    $lineMap[$line['line_id']] = $line['line_name'];
}

// Get stations
foreach($stations as $station) {
    if(!$station['ats_station_id']) continue;
    if(!array_key_exists($station['station_name'], $toiletInfo['shenyang'])) {
        $toiletInfo['shenyang'][$station['station_name']] = ['toilets' => []];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => json_encode(['device_id' => '00', 'line_id' => '00', 'station_id' => $station['station_id']]),
        ],
    ]);
    $stationData = json_decode(file_get_contents($stationApi, false, $context), true)['data'];

    foreach($stationData as $facility) {
        if($facility['device_id'] == '03') {
            foreach(explode(';', $facility['device_location_desc']) as $toilet) {
                $toiletInfo['shenyang'][$station['station_name']]['toilets'][] = [
                    'title' => $lineMap[$facility['line_id']],
                    'content' => $toilet,
                ];
            }
        }
    }
}

$toiletInfo['shenyang']['市府广场'] = ['redirect' => ['人民广场']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['shenyang']).' 条数据');
