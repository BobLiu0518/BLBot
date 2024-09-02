<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['changchun'] = [];
$citiesMeta['changchun'] = [
    'name' => '长春轨道交通',
    'support' => true,
    'source' => '长春e出行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#FA0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_changchun.svg',
];

// Get lines
$lines = json_decode(file_get_contents('http://app.ccetravel.cn/invoices/subwayline/lines'), true)['lines'];
$stationInfoApi = 'http://app.ccetravel.cn/api-truetime/trainRunTime/v107?qtype=1&standcode=';

// Get stations
foreach($lines as $line) {
    $citiesMeta['changchun']['color'][$line['cname']] = $line['linecolour'];
    foreach($line['standList'] as $station) {
        if(!array_key_exists($station['cname'], $toiletInfo['changchun'])) {
            $toiletInfo['changchun'][$station['cname']] = ['toilets' => []];
        }
        $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['standcode']), true);
        foreach($stationInfo['rimMap']['facilitiesInfo'] as $facility) {
            if($facility['facilitiesno'] == '3') {
                $toiletInfo['changchun'][$station['cname']]['toilets'][] = [
                    'title' => $line['cname'],
                    'content' => $facility['facilitiesexplain'],
                ];
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['changchun']).' 条数据');