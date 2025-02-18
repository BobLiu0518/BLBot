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

// Get stations
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'appId: 810280297121',
    ],
]);
$lines = json_decode(file_get_contents('https://aclc.xa-metro.com/apps/v1/api/extend/station/lines/810280297121', false, $context), true)['data'];
$stationDataApi = 'https://aclc.xa-metro.com/apps/v1/api/extend/station/details/810280297121';

// Get stations
foreach($lines as $line) {
    $lineName = preg_replace('/^地铁/u', '', $line['lineName']);
    $citiesMeta['xian']['color'][$lineName] = $line['colour'];
    foreach($line['stationList'] as $station) {
        if(!array_key_exists($station['stationName'], $toiletInfo['xian'])) {
            $toiletInfo['xian'][$station['stationName']] = ['toilets' => []];
        }
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\n", [
                    'Content-Type: application/json',
                    'appId: 810280297121',
                ]),
                'content' => json_encode(['stationNo' => $station['stationNo']]),
            ],
        ]);
        $stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['data'];
        foreach($stationData['contentTabs'][0]['tabContent'] as $facility) {
            if(preg_match('/卫生间/', $facility['contentTitle'])) {
                foreach(explode('；', $facility['contentInfo']) as $toilet) {
                    $toiletInfo['xian'][$station['stationName']]['toilets'][] = [
                        'title' => $lineName,
                        'content' => trim($toilet),
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
    if(preg_match('/(.+)\(.+\)/', $stationName, $match)) {
        if(!array_key_exists($match[1], $toiletInfo['xian'])) {
            $toiletInfo['xian'][$match[1]] = [];
        }
        $toiletInfo['xian'][$match[1]]['redirect'] = [$stationName];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['xian']).' 条数据');
