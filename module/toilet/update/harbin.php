<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['harbin'] = [];
$citiesMeta['harbin'] = [
    'name' => '哈尔滨地铁',
    'support' => '数据截止北马路站开通前',
    'source' => 'Metro冰城行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#FC0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_harbin.svg',
];

// Get lines
$html = file_get_contents('https://ckapp.0451dt.com/ditie/app/pub/station/AppStationInfo/stationInfo.shtml');
preg_match_all('/<li onclick="changeLine\(this\)" data-id="(\d+?)">(.+?)<\/li>/', $html, $match);
$lines = [];
foreach($match[2] as $id => $lineName) {
    $lines[] = [
        'lineName' => $lineName,
        'lineId' => $match[1][$id],
    ];
}
$stationsApi = 'https://ckapp.0451dt.com/ditie/app/pub/station/AppStationInfo/loadStationByLineId.shtml?ID=';
$stationDataPage = 'https://ckapp.0451dt.com/ditie/app/pub/station/AppStationInfo/stationInfomation.shtml?ID=';

// Get stations
foreach($lines as $line) {
    $stations = json_decode(file_get_contents($stationsApi.$line['lineId']), true);
    foreach($stations as $station) {
        if(!preg_match('/^哈尔滨.*站$/', $station['STATION_NAME'])) {
            $station['STATION_NAME'] = preg_replace('/站$/', '', $station['STATION_NAME']);
        }
        if(array_key_exists($station['STATION_NAME'], $toiletInfo['harbin'])) continue;
        $toiletInfo['harbin'][$station['STATION_NAME']] = ['toilets' => []];
        $stationData = file_get_contents($stationDataPage.$station['ID']);
        preg_match('/<div id="tip_3" tip>(.|\n)+?<p>((.|\n)+?)<\/p>(.|\n)+?<\/div>/', $stationData, $match);
        if($match[2]) {
            $toiletInfo['harbin'][$station['STATION_NAME']]['toilets'][] = [
                'title' => '洗手间',
                'content' => trim($match[2]),
            ];
        }
    }
}

// Additional data
$newStations = ['北马路', '兆麟公园', '友谊宫', '上海街', '公路大桥', '河松街', '河山街', '丁香公园'];
foreach($newStations as $station) {
    if(!$toiletInfo['harbin'][$station]) {
        $toiletInfo['harbin'][$station] = [
            'toilets' => [
                [
                    'title' => '无数据',
                    'content' => '由于数据源已停止更新，本站点洗手间位置数据缺失',
                ]
            ],
        ];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['harbin']).' 条数据');
