<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['jinhua'] = [];
$citiesMeta['jinhua'] = [
    'name' => '金华轨道交通',
    'support' => true,
    'source' => '金轨智行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#F20000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_jinhua.svg',
];

// Get lines
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => '{}',
    ],
]);
$lines = json_decode(file_get_contents('https://app.jhmtr.net:65443/ht-app-support-news/app/news/station/getAllStation', false, $context), true)['lines'];
$stationDataApi = 'https://app.jhmtr.net:65443/ht-app-support-news/app/news/stationinside/infos';

// Get stations
foreach($lines as $line) {
    foreach($line['stations'] as $station) {
        if(!preg_match('/^(金华|义乌|横店)(东|南|西|北|高铁)?站$/', $station['stationName'])) {
            $station['stationName'] = preg_replace('/站$/', '', $station['stationName']);
        }
        if(array_key_exists($station['stationName'], $toiletInfo['jinhua'])) continue;
        $toiletInfo['jinhua'][$station['stationName']] = ['toilets' => []];
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode([
                    'stationCode' => $station['stationCode'],
                ]),
            ],
        ]);
        $stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['lineStationPeripheryList'][0];
        foreach($stationData['toiletList'] as $toilet) {
            if($toilet['serviceName'] == '卫生间') {
                foreach(explode('/', $toilet['address']) as $toilet) {
                    $toiletInfo['jinhua'][$station['stationName']]['toilets'][] = [
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
replyAndLeave('更新数据成功，共 '.count($toiletInfo['jinhua']).' 条数据');