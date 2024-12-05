<?php

requireLvl(6);
loadModule('toilet.update.enfc');

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['beijing'] = [];
$citiesMeta['beijing'] = [
    'name' => '北京地铁',
    'support' => true,
    'source' => '亿通行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#1B0082',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_beijing.svg',
];

$map = json_decode(file_get_contents('https://appconfig.ruubypay.com/stations/map-app.json'), true);
$accLocation = json_decode(file_get_contents('https://appconfig.ruubypay.com/stations/acclocation.json'), true);

$lineMap = [];
foreach($map['lines_data'] as $line) {
    $lineName = preg_replace('/^(\d+号线)(.+线)$/', '$1·$2', $line['cn_name']);
    $lineMap[$line['id']] = $lineName;
    $citiesMeta['beijing']['color'][$lineName] = '#'.$line['color'];
}

$stationsMap = [];
foreach($map['stations_data'] as $station) {
    $stationsMap[$station['id']] = $station['cn_name'];
}

foreach($accLocation as $device) {
    $deviceData = getStationEquipment($device['device_location']);
    if($deviceData['device'][0]) {
        $toiletInfo['beijing'][$stationsMap[$device['station_id']]]['toilets'][] = [
            'title' => $lineMap[$device['line_id']],
            'content' => $deviceData['device'][0]['restroom'],
        ];
    }
}

foreach($toiletInfo['beijing'] as &$data) {
    usort($data['toilets'],
        fn($a, $b) =>
        (preg_match('/^\d+/', $a['title'], $m1) ? intval($m1[0]) : INF)
        <=>
        (preg_match('/^\d+/', $b['title'], $m2) ? intval($m2[0]) : INF)
        ?: $a['title'] <=> $b['title']
    );
}

// Virtual transfer
$toiletInfo['beijing']['复兴门']['redirect'] = ['太平桥'];
$toiletInfo['beijing']['太平桥']['redirect'] = ['复兴门'];
$toiletInfo['beijing']['广安门内']['redirect'] = ['牛街'];
$toiletInfo['beijing']['牛街']['redirect'] = ['广安门内'];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['beijing']).' 条数据');