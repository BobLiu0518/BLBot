<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['taizhou'] = [];
$citiesMeta['taizhou'] = [
    'name' => '台州轨道交通',
    'support' => true,
    'source' => '台州畅行轨道官方网站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#ED0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_taizhou.svg',
];

// Get stations
$stations = json_decode(file_get_contents('https://www.tz-mtr.com/tzsgdjt/gdjt/api/sidebarTree/stationList'), true)['result'];

// Get data
foreach($stations as $station) {
    $stationName = preg_replace('/站$/', '', $station['name']);
    $toiletInfo['taizhou'][$stationName] = ['toilets' => []];
    preg_match('/^洗手间位置：(.+?);?$/m', $station['facility'], $match);
    if($match[1]) {
        $toiletInfo['taizhou'][$stationName]['toilets'][] = [
            'title' => '卫生间',
            'content' => $match[1],
        ];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['taizhou']).' 条数据');