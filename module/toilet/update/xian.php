<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['xian'] = [];
$citiesMeta['xian'] = [
    'name' => '西安地铁',
    'support' => true,
    'source' => '西安地铁 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#DC000E',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_xian.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://xm-cdn.oss-cn-hangzhou.aliyuncs.com/json/stationData.json', false, $context), true)['data'];
$stationDataApi = 'https://xadt.i-xiaoma.com.cn/api/v2/app/stationInfo';

// Get stations
foreach($lines as $line) {
    $citiesMeta['xian']['color'][$line['lineName']] = $line['lineColor'];
    foreach($line['lineStationList'] as $station) {
        if(!array_key_exists($station['stationName'], $toiletInfo['xian'])) {
            $toiletInfo['xian'][$station['stationName']] = ['toilets' => []];
        }
        $toilets = [];
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode(['stationId' => $station['stationId']]),
            ],
        ]);
        $stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['data'];
        foreach($stationData['facility'] as $facility) {
            if(preg_match('/卫生间/', $facility['facilityName'])) {
                foreach(explode('；', $facility['facilityDesc']) as $toilet) {
                    $toiletInfo['xian'][$station['stationName']]['toilets'][] = [
                        'title' => $line['lineName'],
                        'content' => $toilet,
                    ];
                }
            }
        }
    }
}

// Station name aliases
foreach(array_keys($toiletInfo['xian']) as $stationName) {
    if(preg_match('/·/u', $stationName)) {
        foreach(explode('·', $stationName) as $subName) {
            if(!array_key_exists($subName, $toiletInfo['xian'])) {
                $toiletInfo['xian'][$subName] = [];
            }
            $toiletInfo['xian'][$subName]['redirect'] = [$stationName];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['xian']).' 条数据');