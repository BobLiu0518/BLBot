<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['qingdao'] = [];
$citiesMeta['qingdao'] = [
    'name' => '青岛地铁',
    'support' => true,
    'source' => '青岛地铁 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#00553E',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_qingdao.svg',
];

// Get lines
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => 'version=0',
    ],
]);
$lines = json_decode(file_get_contents('https://api.qd-metro.com/ngstatic/station/toStation', false, $context), true)['data']['stationData'];
$stationDataApi = 'https://api.qd-metro.com/ngstatic/station/newStationInfo320';

// Get stations
foreach($lines as $line) {
    $citiesMeta['qingdao']['color'][$line['lineName']] = $line['stationData'][0]['textLocation'];
    foreach($line['stationData'] as $station) {
        if(array_key_exists($station['name'], $toiletInfo['qingdao'])) continue;
        $toiletInfo['qingdao'][$station['name']] = ['toilets' => []];
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => 'version=0&stationId='.$station['id'],
            ],
        ]);
        $stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['data'];
        foreach($stationData['installation'] as $facility) {
            if(preg_match('/卫生间/', $facility['name'])) {
                foreach(preg_split('/\n|；/u', $facility['address']) as $toilet) {
                    if($toilet) {
                        $toiletInfo['qingdao'][$station['name']]['toilets'][] = [
                            'title' => '卫生间',
                            'content' => $toilet,
                        ];
                    }
                }
            }
        }
    }
}

// Station name aliases
foreach(array_keys($toiletInfo['qingdao']) as $stationName) {
    if(preg_match('/^(.+?)\(.+\)$/u', $stationName, $match)) {
        if(!array_key_exists($match[1], $toiletInfo['qingdao'])) {
            $toiletInfo['qingdao'][$match[1]] = [];
        }
        $toiletInfo['qingdao'][$match[1]]['redirect'] = [$stationName];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['qingdao']).' 条数据');