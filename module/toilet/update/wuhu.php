<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['wuhu'] = [];
$citiesMeta['wuhu'] = [
    'name' => '芜湖轨道交通',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#008CD3',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_wuhu.svg',
];

// Get lines
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => 'action=get-all-info&start=&end=',
    ],
]);
$lines = json_decode(file_get_contents('http://www.wuhurailtransit.com/Ajax/api.ashx', false, $context), true);

// Get stations
foreach($lines as $line) {
    foreach($line as $station) {
        $toiletInfo['wuhu'][$station['Title']] = [];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['wuhu']).' 条数据');