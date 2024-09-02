<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['tianjin'] = [];
$citiesMeta['tianjin'] = [
    'name' => '天津地铁',
    'support' => true,
    'source' => '天津地铁运营微信公众号找厕所页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#FF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_tianjin.svg',
];

// Get stations
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['stationName' => '']),
    ],
]);
$stations = json_decode(file_get_contents('http://47.92.88.178:20089/api/app/lineStation/lineStationList', false, $context), true)['data'];
$stations = $stations[0]['stationList'];
$toiletsApi = 'http://47.92.88.178:20089/api/app/lineStation/restroomDetail';

// Get toilets
foreach($stations as $station) {
    $toiletInfo['tianjin'][$station['stationName']] = ['toilets' => []];
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode(['stationId' => $station['stationId']]),
        ],
    ]);
    $toilets = json_decode(file_get_contents($toiletsApi, false, $context), true)['data'];
    foreach($toilets as $toilet) {
        if($toilet['classification'] == '0' && $toilet['address']) {
            if(preg_match('/^(.+)——(.+)$/u', $toilet['address'], $match)) {
                $toiletInfo['tianjin'][$station['stationName']]['toilets'][] = [
                    'title' => $match[1],
                    'content' => $match[2],
                ];
            } else {
                $toiletInfo['tianjin'][$station['stationName']]['toilets'][] = [
                    'title' => '卫生间',
                    'content' => $toilet['address'],
                ];
            }
        }
    }
}

// Out-of-station transfer
$toiletInfo['tianjin']['小白楼']['redirect'] = ['徐州道'];
$toiletInfo['tianjin']['徐州道']['redirect'] = ['小白楼'];
$toiletInfo['tianjin']['左江道']['redirect'] = ['友谊南路'];
$toiletInfo['tianjin']['友谊南路']['redirect'] = ['左江道'];
$toiletInfo['tianjin']['一号桥']['redirect'] = ['龙涵道'];
$toiletInfo['tianjin']['龙涵道']['redirect'] = ['一号桥'];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['tianjin']).' 条数据');