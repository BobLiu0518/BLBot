<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['jinan'] = [];
$citiesMeta['jinan'] = [
    'name' => '济南轨道交通',
    'support' => true,
    'source' => '济南地铁 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#0077C9',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_jinan.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://app.jngdjt.cn:8889/app/appTicketLineController/queryAllMapLines'), true)['map'];
$stationDataApi = 'https://app.jngdjt.cn:8889/app/stationController/getLineStationDetailInfoAPP';

// Get stations
foreach($lines['lines'] as $line) {
    foreach($line['stations'] as $station) {
        if(array_key_exists($station['name'], $toiletInfo['jinan'])) continue;
        $toiletInfo['jinan'][$station['name']] = ['toilets' => []];
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode([
                    'stationCode' => $station['stationCode']
                ]),
            ],
        ]);
        $stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['lineStationPeripheryList'][0];
        foreach($stationData['peripheralServiceList'] as $facility) {
            if(preg_match('/卫生间/u', $facility['serviceName'])) {
                $toiletInfo['jinan'][$station['name']]['toilets'][] = [
                    'title' => '卫生间',
                    'content' => $facility['address'],
                ];
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['jinan']).' 条数据');