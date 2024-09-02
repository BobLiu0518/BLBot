<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['wuhan'] = [];
$citiesMeta['wuhan'] = [
    'name' => '武汉地铁',
    'support' => true,
    'source' => '武汉地铁运营微信公众号车站信息查询页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#1B0082',
        'secondary' => '#FF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_wuhan.svg',
];

// Get lines
$context = stream_context_create([
    'http' => ['method' => 'POST'],
]);
$lines = json_decode(file_get_contents('https://advh5.whrtmpay.com/siteInfo/getLineAndStation', false, $context), true)['rtData'];
$facilitiesApi = 'https://advh5.whrtmpay.com/siteInfo/getStationFacility';

// Get station data
foreach($lines as $line) {
    foreach($line['stationInfos'] as $station) {
        $toiletInfo['wuhan'][$station['siteName']] = ['toilets' => []];
        $toilets = [];
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode(['siteCode' => $station['siteCode']]),
            ],
        ]);
        $facilities = json_decode(file_get_contents($facilitiesApi, false, $context), true)['rtData'];
        foreach($facilities as $facility) {
            if($facility['toiletPublic']) {
                foreach(explode('，', $facility['toiletPublic']) as $toilet) {
                    $toilets[] = $toilet;
                }
            }
        }
        $toilets = array_unique($toilets);
        foreach($toilets as $toilet) {
            $toiletInfo['wuhan'][$station['siteName']]['toilets'][] = [
                'title' => '卫生间',
                'content' => $toilet,
            ];
        }
    }
}

// Weird Wuhan railway station
$toiletInfo['wuhan']['武汉火车站']['redirect'] = ['武汉站东广场', '武汉站西广场'];
$toiletInfo['wuhan']['武汉站东广场']['redirect'] = ['武汉火车站', '武汉站西广场'];
$toiletInfo['wuhan']['武汉站西广场']['redirect'] = ['武汉火车站', '武汉站东广场'];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['wuhan']).' 条数据');