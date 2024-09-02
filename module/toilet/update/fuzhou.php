<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['fuzhou'] = [];
$citiesMeta['fuzhou'] = [
    'name' => '福州地铁',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#00983A',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_fuzhou.svg',
];

// Get stations
$stationsPage = file_get_contents('http://www.fzmtr.com/html/fzdt/index.html');
preg_match_all('/<SPAN class="name">(.+?)<\/SPAN>/', $stationsPage, $match);
foreach($match[1] as $station) {
    $toiletInfo['fuzhou'][$station] = [];
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['fuzhou']).' 条数据');