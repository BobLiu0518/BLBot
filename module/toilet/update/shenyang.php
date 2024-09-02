<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['shenyang'] = [];
$citiesMeta['shenyang'] = [
    'name' => '沈阳地铁',
    'support' => true,
    'source' => '沈阳地铁微信公众号站点信息页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#DC000E',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_shenyang.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://external-website.nsmetro.com/api/mp-api/line'), true)['result'];
$stationApi = 'https://external-website.nsmetro.com/api/mp-api/station';

// Get stations
foreach($lines as $line) {
    $stations = json_decode(file_get_contents($stationApi.'?lineCode='.$line['lineCode']), true)['result'];
    foreach($stations as $station) {
        if(!array_key_exists($station['stationName'], $toiletInfo['shenyang'])) {
            $toiletInfo['shenyang'][$station['stationName']] = ['toilets' => []];
        }

        $stationData = json_decode(file_get_contents($stationApi.'/'.$station['id']), true)['result'];
        foreach($stationData['facilities'] as $facility) {
            if($facility['facilityId'] == 16) {
                foreach(explode('、', $facility['facilityValue']) as $toilet) {
                    $toiletInfo['shenyang'][$station['stationName']]['toilets'][] = [
                        'title' => preg_replace('/^地铁/', '', $line['lineName']),
                        'content' => $toilet,
                    ];
                }
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['shenyang']).' 条数据');