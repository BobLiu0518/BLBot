<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['taichung'] = [];
$citiesMeta['taichung'] = [
    'name' => '臺中捷運',
    'support' => true,
    'source' => '臺中捷運網站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#0093DE',
        'secondary' => '#7DC500',
    ],
    'font' => 'TW',
    'logo' => 'metro_logo_taichung.svg',
];

// Get data
$link = 'https://www.tmrt.com.tw/metro-life/station-information?id=';
$html = file_get_contents($link);
preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.+?)<\/script>/', $html, $match);
$stations = json_decode($match[1], true)['props']['pageProps']['stationData'];

// Set data
foreach($stations as $station) {
    $toiletInfo['taichung'][$station['SiteName']] = ['toilets' => []];
    $html = file_get_contents($link.$station['ID']);
    preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.+?)<\/script>/', $html, $match);
    $facilities = json_decode($match[1], true)['props']['pageProps']['siteFacilityTableData'];
    foreach($facilities as $facility) {
        if($facility['FacilityName'] == '洗手間') {
            $toiletInfo['taichung'][$station['SiteName']]['toilets'][] = [
                'title' => '洗手間',
                'content' => $facility['Position'],
            ];
        }
    }

    if(OpenCC::tw2s($station['SiteName']) != $station['SiteName']) {
        $toiletInfo['taichung'][OpenCC::tw2s($station['SiteName'])] = [
            'redirect' => [$station['SiteName']],
        ];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['taichung']).' 条数据');
