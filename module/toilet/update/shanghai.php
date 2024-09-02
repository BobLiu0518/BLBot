<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['shanghai'] = [];
$citiesMeta['shanghai'] = [
    'name' => '上海地铁',
    'support' => true,
    'source' => '上海地铁官方网站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#EC1B23',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_shanghai.svg',
];

// Load stations
$stations = json_decode(file_get_contents('https://m.shmetro.com/core/shmetro/mdstationinfoback_new.ashx?act=getAllStations'), true);
$stationInfoApi = 'https://m.shmetro.com/interface/metromap/metromap.aspx?func=stationInfo&stat_id=';
$lines = json_decode(file_get_contents('https://m.shmetro.com/interface/metromap/metromap.aspx?func=lines'), true);
foreach($stations as $station) {
    // Load data
    $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['key']), true)[0];
    $stationName = $station['value'];
    $toilets = json_decode($stationInfo['toilet_position'], true)['toilet'];

    // Skip fake stations
    if(preg_match('/^[内外]圈(\(宜山路\))?/u', $stationName)) continue;

    // Handle Pudian Road station conflict
    if($stationName == '浦电路') $stationName = '浦电路（4号线）';
    else if($stationName == '浦电路 ') $stationName = '浦电路（6号线）';

    // Remove redundant "站"
    else if(preg_match('/^外高桥保税区[南北]站$/u', $stationName)) $stationName = preg_replace('/站$/', '', $stationName);

    // Match data
    $toiletInfo['shanghai'][$stationName] = ['toilets' => []];
    foreach($toilets as $toilet) {
        if($toilet['lineno'] == 41) {
            $lineName = '浦江线';
        } else {
            $lineName = $toilet['lineno'].'号线';
        }

        $toiletInfo['shanghai'][$stationName]['toilets'][] = [
            'title' => $lineName,
            'content' => $toilet['description'],
        ];
    }
}

// Set line color
foreach($lines as $line) {
    if($line['line_no'] == 41) {
        $lineName = '浦江线';
    } else {
        $lineName = $line['line_no'].'号线';
    }
    $citiesMeta['shanghai']['color'][$lineName] = $line['color'];
}

// Handle Pudian Road station conflict
$toiletInfo['shanghai']['浦电路'] = ['redirect' => ['浦电路（4号线）', '浦电路（6号线）']];

// Set aliases
$toiletInfo['shanghai']['黄陂南路'] = ['redirect' => ['一大会址·黄陂南路']];
$toiletInfo['shanghai']['新天地'] = ['redirect' => ['一大会址·新天地']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['shanghai']).' 条数据');