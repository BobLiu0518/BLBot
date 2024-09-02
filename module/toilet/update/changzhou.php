<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['changzhou'] = [];
$citiesMeta['changzhou'] = [
    'name' => '常州地铁',
    'support' => true,
    'source' => '常州地铁微信公众号乘车指南页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#DA0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_changzhou.svg',
];

// Get lines
$context = stream_context_create([
    'http' => [
        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
    ],
]);
$lines = json_decode(file_get_contents('https://www.czmetro.net.cn/Html/Admin/Web/Station/getStationList', false, $context), true)['data'];
$stationInfoApi = 'https://www.czmetro.net.cn/Html/Admin/Web/Station/getStationDetail?station_id=';

// Get stations
foreach($lines as $line) {
    foreach($line['station_data'] as $station) {
        if(!in_array($station['name'], ['常州火车站', '常州北站', '常州南站'])) {
            $station['name'] = preg_replace('/站$/', '', $station['name']);
        }
        if(!array_key_exists($station['name'], $toiletInfo['changzhou'])) {
            $toiletInfo['changzhou'][$station['name']] = ['toilets' => []];
        }
        $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['id'], false, $context), true)['data'];
        if($stationInfo['wc_position']) {
            $toiletInfo['changzhou'][$station['name']]['toilets'][] = [
                'title' => $stationInfo['line_name'],
                'content' => $stationInfo['wc_position'],
            ];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['changzhou']).' 条数据');