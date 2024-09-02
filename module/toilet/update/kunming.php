<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['kunming'] = [];
$citiesMeta['kunming'] = [
    'name' => '昆明地铁',
    'support' => true,
    'source' => '昆明地铁App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#1740FF',
        'secondary' => '#84D600',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_kunming.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://zhcx.km-metro.com/api/station_info/init'), true)['data']['stationSelectData'];
$stationDataApi = 'https://zhcx.km-metro.com/api/station_info/get_data?stationName=null&stationCode=';

// Get stations
foreach($lines as $line) {
    $citiesMeta['kunming']['color'][$line['label']] = $line['color'];
    foreach($line['children'] as $station) {
        if(!preg_match('/^.+(汽车站|火车.*站)$/', $station['label'])) {
            $station['label'] = preg_replace('/站$/', '', $station['label']);
        }
        if(!array_key_exists($station['label'], $toiletInfo['kunming'])) {
            $toiletInfo['kunming'][$station['label']] = ['toilets' => []];
        }
        $stationData = json_decode(file_get_contents($stationDataApi.$station['value']), true)['data'];
        foreach($stationData['stationServiceList'] as $stationService) {
            if($stationService['serviceType'] == '4') {
                foreach($stationService['serviceInfos'] as $toilet) {
                    $toiletInfo['kunming'][$station['label']]['toilets'][] = [
                        'title' => $line['label'],
                        'content' => $toilet['servicePosition'],
                    ];
                }
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['kunming']).' 条数据');