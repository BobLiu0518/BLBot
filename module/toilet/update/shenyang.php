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
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
    ],
]);
$lines = json_decode(file_get_contents('https://www.symtc.com/portalManager/selectLineInfoByDC', false, $context), true)['result'];
$lineMap = [];
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/json',
        'content' => json_encode(['lineId' => '00']),
    ],
]);
$stations = json_decode(file_get_contents('https://www.symtc.com/portalManager/selectStationInfoByDC', false, $context), true)['result'];
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/json',
        'content' => json_encode(['device_id' => '00', 'line_id' => '00', 'station_id' => '0000']),
    ],
]);
$devices = json_decode(file_get_contents('https://www.symtc.com/portalManager/selectDeviceInfoByDC', false, $context), true)['result'];

foreach($lines as $line) {
    $citiesMeta['shenyang']['color'][$line['lineName']] = $line['lineColor'];
    $lineMap[$line['lineId']] = $line['lineName'];
}

// Get stations
foreach($stations as $station) {
    if(!$station['atsStationId']) continue;
    $toiletInfo['shenyang'][$station['stationName']] = ['toilets' => []];
}

foreach($devices as $device) {
    if($device['deviceId'] == '03') {
        foreach(explode(';', $device['deviceLocationDesc']) as $toilet) {
            $toiletInfo['shenyang'][$device['stationName']]['toilets'][] = [
                'title' => $lineMap[$device['lineId']],
                'content' => $toilet,
            ];
        }
    }
}

$toiletInfo['shenyang']['市府广场'] = ['redirect' => ['人民广场']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['shenyang']).' 条数据');
