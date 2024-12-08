<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['macau'] = [];
$citiesMeta['macau'] = [
    'name' => '澳門輕軌',
    'support' => false,
    'source' => null,
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#0092DA',
        'secondary' => '#5AAC2C',
    ],
    'font' => 'HK',
    'logo' => 'metro_logo_macau.svg',
];

// Load stations
$html = file_get_contents('https://www.mlm.com.mo/tc/route.html');
preg_match_all('/\{\s+?text: "(.+?)站(?:[（(].+?[）)])?",\s+?value: ".+?",?\s+?}/us', $html, $match);
$stations = array_unique($match[1]);
foreach($stations as $station) {
    $toiletInfo['macau'][$station] = [];
    // TC -> SC
    if(OpenCC::hk2s($station) != $station) {
        $toiletInfo['macau'][OpenCC::hk2s($station)] = ['redirect' => [$station]];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['macau']).' 条数据');