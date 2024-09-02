<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['changsha'] = [];
$citiesMeta['changsha'] = [
    'name' => '长沙轨道交通',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#16025E',
        'secondary' => '#FF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_changsha.svg',
];

// Get lines
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['pageSize' => 0, 'pageNum' => 0, 'appDeviceType' => 2]),
    ],
]);
$lines = json_decode(file_get_contents('https://itp.hncsmtr.com:8889/app/stationController/allstation', false, $context), true)['lines'];

// Get stations
foreach($lines as $line) {
    foreach($line['stations'] as $station) {
        $toiletInfo['changsha'][$station['stationName']] = [];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['changsha']).' 条数据');