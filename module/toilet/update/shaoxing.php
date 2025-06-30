<?php

require('request.php');
requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['shaoxing'] = [];
$citiesMeta['shaoxing'] = [
    'name' => '绍兴轨道交通',
    'support' => '仅含京越地铁运营的1号线（含支线）数据，不含其他公司运营的2号线、S1线',
    'source' => '京越地铁官方网站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#D10027',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_shaoxing.svg',
];

// Get Shaoxing Jingyue Metro data
$stations = json_decode(file_get_contents('http://www.jymetro.com.cn/index/site/getsites.html?lineid=1'), true)['data'];
$stationDataPage = 'http://www.jymetro.com.cn/index/site/info.html?id=';
foreach($stations as $station) {
    $station['title'] = preg_replace('/站$/', '', $station['title']);
    $stationData = file_get_contents($stationDataPage.$station['id']);
    $toiletInfo['shaoxing'][$station['title']] = ['toilets' => []];
    preg_match('/<p>卫生间<\/p><p>(.+?)<\/p>/', str_replace('&nbsp;', '', $stationData), $match);
    if($match[1]) {
        foreach(explode('、', $match[1]) as $toilet) {
            $toiletInfo['shaoxing'][$station['title']]['toilets'][] = [
                'title' => '卫生间',
                'content' => $toilet,
            ];
        }
    }
}

// Get Shaoxing Metro data
$cityId = '3301';
$realCityId = '3306';
$lines = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/line/list', ['service_id' => '01', 'city_id' => $realCityId]), true)['result'];
foreach($lines as $line) {
    $stations = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 2000, 'line_no' => $line['line_no'], 'service_id' => '01', 'city_id' => $realCityId]), true)['result']['rows'];
    foreach($stations as $station) {
        if(!array_key_exists($station['station_name'], $toiletInfo['shaoxing'])) {
            $toiletInfo['shaoxing'][$station['station_name']] = ['toilets' => []];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['shaoxing']).' 条数据');
