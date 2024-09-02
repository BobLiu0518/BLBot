<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['wenzhou'] = [];
$citiesMeta['wenzhou'] = [
    'name' => '温州轨道交通',
    'support' => true,
    'source' => '温州轨道 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#C80330',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_wenzhou.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://app.wzmtr.com:6443/inner-service/line/searchLineList'), true)['data'];
$lineDataApi = 'https://app.wzmtr.com:6443/inner-service/siteDetails/searchSiteList?lineNo=';
$stationDataApi = 'https://app.wzmtr.com:6443/inner-service/siteDetails/searchStationInfo?siteId=';

// Get stations
foreach($lines as $line) {
    $stations = json_decode(file_get_contents($lineDataApi.$line['lineNo']), true)['data'];
    foreach($stations as $station) {
        $station['name'] = preg_replace('/站$/u', '', $station['name']);
        if(!array_key_exists($station['name'], $toiletInfo['wenzhou'])) {
            $toiletInfo['wenzhou'][$station['name']] = ['toilets' => []];
        }
        $stationData = json_decode(file_get_contents($stationDataApi.$station['staNo']), true)['data'];
        foreach($stationData['stationFacility'] as $facility) {
            if(preg_match('/(洗手间|卫生间)/u', $facility['facilityName'])) {
                $toiletInfo['wenzhou'][$station['name']]['toilets'][] = [
                    'title' => $line['name'],
                    'content' => $facility['facilityLocation'],
                ];
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['wenzhou']).' 条数据');