<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['nanning'] = [];
$citiesMeta['nanning'] = [
    'name' => '南宁轨道交通',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#18BEF2',
        'secondary' => '#0056A4',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_nanning.svg',
];

// Get HTML
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: Mozilla/5.0',
    ],
]);
$stationsPage = file_get_contents('https://www.nngdjt.com/html/service1c/', false, $context);
preg_match_all('/<span class="station" id="\d+"\s*>\s*(.+)\s*<\/span>/', $stationsPage, $match);
foreach($match[1] as $station) {
    if(!preg_match('/(客运|火车)(东|南|西|北)?站$/', $station)) {
        $station = preg_replace('/站$/', '', $station);
    }
    $toiletInfo['nanning'][trim($station)] = [];
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['nanning']).' 条数据');