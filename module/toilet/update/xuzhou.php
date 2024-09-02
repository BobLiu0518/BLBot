<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['xuzhou'] = [];
$citiesMeta['xuzhou'] = [
    'name' => '徐州地铁',
    'support' => true,
    'source' => '徐州地铁 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#FF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_xuzhou.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://mallmetroinfo.xzsmartmetro.com/Api/StationLines/StationLineTimes'), true)['Data'];
$stationInfoApi = 'https://mallmetroinfo.xzsmartmetro.com/Api/Stations/';

// Get data
foreach($lines as $line) {
    // Skip different direction of the same line
    if($line['Flag'] == 2) continue;

    // Get stations
    foreach($line['StationTimes'] as $station) {
        if(!array_key_exists($station['StationName'], $toiletInfo['xuzhou'])) {
            $toiletInfo['xuzhou'][$station['StationName']] = ['toilets' => []];
        }
        $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['StationId'].'/Around'), true)['Data'];
        foreach($stationInfo['StationFacilties'] as $facility) {
            if($facility['SubCategory'] == 104) {
                foreach(preg_split('/、|，/u', $facility['Description']) as $toilet) {
                    if(preg_match('/^(.+)（(.+号线)）$/u', $toilet, $match)) {
                        $toiletInfo['xuzhou'][$station['StationName']]['toilets'][] = [
                            'title' => $match[2],
                            'content' => $match[1],
                        ];
                    } else {
                        $toiletInfo['xuzhou'][$station['StationName']]['toilets'][] = [
                            'title' => $station['LineName'],
                            'content' => $toilet,
                        ];
                    }
                }
            }
        }
        $toiletInfo['xuzhou'][$station['StationName']]['toilets'] = array_unique($toiletInfo['xuzhou'][$station['StationName']]['toilets'], SORT_REGULAR);
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['xuzhou']).' 条数据');