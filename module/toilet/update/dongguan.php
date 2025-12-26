<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['dongguan'] = [];
$citiesMeta['dongguan'] = [
    'name' => '东莞轨道交通',
    'support' => true,
    'source' => '东莞地铁 App',
    'time' => time(),
    'color' => [
        'main' => '#78C123',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_dongguan.svg',
];

$mapData = json_decode(file_get_contents('https://itpstatic.dggdjt.com/stations/map-app.json'), true);
$accData = json_decode(file_get_contents('https://itpstatic.dggdjt.com/stations/acclocation.json'), true);
$facApi = 'https://itpapi.dggdjt.com/marketingpis/appSides/getStationFacility';

$lineMap = [];
$stationMap = [];

foreach($mapData['lines_data'] as $line) {
    $lineMap[$line['id']] = $line['cn_name'];
    $citiesMeta['dongguan']['color'][$line['cn_name']] = "#{$line['color']}";
}

foreach($mapData['stations_data'] as $station) {
    $stationMap[$station['id']] = $station['cn_name'];
}

foreach($accData as $acc) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode([
                'cityCode' => '5230',
                'deviceLocation' => strval($acc['device_location']),
            ]),
        ],
    ]);
    $facData = json_decode(file_get_contents($facApi, false, $context), true);

    foreach($facData['resData']['device'] as $device){
        if($device['facilityType'] != 3) continue;

        $stationName = str_replace('（地铁）', '', $stationMap[$acc['station_id']]);
        $lineName = $lineMap[$acc['line_id']];

        if(!$toiletInfo['dongguan'][$stationName]){
            $toiletInfo['dongguan'][$stationName] = ['toilets' => []];
        }
        $toiletInfo['dongguan'][$stationName]['toilets'][] = [
            'title' => $lineName,
            'content' => $device['facilityDesc'],
        ];
    }
}

// Handle metro & intercity railway interchange
$toiletInfo['dongguan']['西平西'] = ['redirect' => ['西平']];

// Handle Hongfu Road station name change (temporary)
$toiletInfo['dongguan']['鸿福路'] = ['redirect' => ['市民中心']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['dongguan']).' 条数据');
