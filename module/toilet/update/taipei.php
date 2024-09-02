<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['taipei'] = [];
$citiesMeta['taipei'] = [
    'name' => '臺北捷運',
    'support' => true,
    'source' => '臺北大眾捷運網站',
    'remark' => '含新北大眾捷運環狀線，不含淡海輕軌、安坑輕軌',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#007AAF',
        'secondary' => '#3FAF35',
    ],
    'font' => 'TW',
    'logo' => 'metro_logo_taipei.svg',
];

// Get data
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode([
            'LineID' => '0',
            'Lang' => 'tw',
        ]),
    ],
]);
$lines = json_decode(file_get_contents('https://web.metro.taipei/apis/metrostationapi/menuline', false, $context), true);
$stationDataApi = 'https://web.metro.taipei/apis/metrostationapi/stationdetail';

// Set data
foreach($lines as $line) {
    foreach($line['LineStations'] as $station) {
        if(array_key_exists($station['StationName'], $toiletInfo['taipei'])) continue;
        $toiletInfo['taipei'][$station['StationName']] = ['toilets' => []];
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode([
                    'SID' => $station['SID'],
                    'Lang' => 'tw',
                ]),
            ],
        ]);
        $stationData = json_decode(file_get_contents($stationDataApi, false, $context), true);
        $toilets = [];
        $belongingLine = null;
        foreach(explode('<br>', $stationData['stationInfo']['Restroom'] ?? '') as $toilet) {
            $toilet = preg_replace('/^\s+|\s+$|"/', '', $toilet);
            if($toilet) {
                if(preg_match('/^(.+線)：.+$/', $toilet, $match) || preg_match('/^.+\((.+線)\)$/', $toilet, $match)) {
                    $belongingLine = $match[1];
                } else {
                    $toiletInfo['taipei'][$station['StationName']]['toilets'][] = [
                        'title' => $belongingLine ?? '廁所',
                        'content' => $toilet,
                    ];
                }
            }
        }
        if(OpenCC::tw2s($station['StationName']) != $station['StationName']) {
            $toiletInfo['taipei'][OpenCC::tw2s($station['StationName'])] = [
                'redirect' => [$station['StationName']],
            ];
        }
    }
}

// Out of station transfer
$toiletInfo['taipei']['新埔']['redirect'] = ['新埔民生'];
$toiletInfo['taipei']['新埔民生']['redirect'] = ['新埔'];
$toiletInfo['taipei']['台北車站']['redirect'] = ['北門'];
$toiletInfo['taipei']['北門']['redirect'] = ['台北車站'];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['taipei']).' 条数据');
