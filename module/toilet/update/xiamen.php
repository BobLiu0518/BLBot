<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['xiamen'] = [];
$citiesMeta['xiamen'] = [
    'name' => '厦门地铁',
    'support' => true,
    'source' => '厦门地铁 App',
    'time' => date('Y/m/d', filemtime(getCachePath('toilet/xiamenStations.json'))),
    'color' => [
        'main' => '#E80026',
        'secondary' => '#00277E',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_xiamen.svg',
];

// Install Xiamen Metro App on Android phone
// Packet capture: https://app.i-amtr.com/iets-web-app/pub/station/allStation
$stations = json_decode(getCache('toilet/xiamenStations.json'), true)['responseData'];
$stations = openssl_decrypt(hex2bin($stations), 'aes-128-ecb', 'QLJ1aZjhhTEm6RiN', OPENSSL_RAW_DATA);
$stations = json_decode($stations, true)['data'];

// Get data
foreach($stations as $station) {
    if(!array_key_exists($station['name'], $toiletInfo['xiamen'])) {
        $toiletInfo['xiamen'][$station['name']] = ['toilets' => []];
    }
    $facilities = json_decode($station['facilities'], true);
    if($facilities['洗手间']) {
        $toiletInfo['xiamen'][$station['name']]['toilets'][] = [
            'title' => $station['lineName'],
            'content' => $facilities['洗手间'],
        ];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['xiamen']).' 条数据（更新时间 '.$citiesMeta['xiamen']['time'].'）');