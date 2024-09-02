<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['beijing'] = [];
$citiesMeta['beijing'] = [
    'name' => '北京地铁',
    'support' => true,
    'source' => '北京轨道运营微信小程序',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#1B0082',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_beijing.svg',
];

// Get data
$lines = json_decode(file_get_contents('https://xiaochengxu.bjmoa.cn/shop/api/subway/subwayAllInfo'), true)['data'];
$stationInfoApi = 'https://xiaochengxu.bjmoa.cn/shop/api/subway/stationInfo?stationDbid=';
foreach($lines as $line) {
    foreach($line['allStationInfos'] as $station) {
        if(array_key_exists($station['stationName'], $toiletInfo['beijing'])) continue;
        $toiletInfo['beijing'][$station['stationName']] = ['toilets' => []];
        $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['stationDbid']), true)['data'];
        foreach($stationInfo['stationInfoServiceBase']['allService'] as $facility) {
            if($facility['serviceFacilityName'] == '卫生间') {
                $toiletInfo['beijing'][$station['stationName']]['toilets'][] = [
                    'title' => $facility['serviceFacilityName'],
                    'content' => $facility['serviceFacilityExit'],
                ];
            }
        }
    }
}

// Virtual transfer
$toiletInfo['beijing']['复兴门']['redirect'] = ['太平桥'];
$toiletInfo['beijing']['太平桥']['redirect'] = ['复兴门'];
$toiletInfo['beijing']['广安门内']['redirect'] = ['牛街'];
$toiletInfo['beijing']['牛街']['redirect'] = ['广安门内'];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['beijing']).' 条数据');