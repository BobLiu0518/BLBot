<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['ningbo'] = [];
$citiesMeta['ningbo'] = [
    'name' => '宁波轨道交通',
    'support' => true,
    'source' => '宁波轨道交通微信公众号车站信息页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#138EEB',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_ningbo_plain.svg',
];

// Get stations
$stations = json_decode(file_get_contents('https://metroinfo.ditiego.net/Api/StationLines/GetStationList', false, $context), true)['Data'];
$stationInfoApi = 'https://metroinfo.ditiego.net/Api/Stations/';
foreach($stations as $station) {
    $station['StationName'] = preg_replace('/（.+）$/u', '', $station['StationName']);
    if(array_key_exists($station['StationName'], $toiletInfo['ningbo'])) continue;
    $toiletInfo['ningbo'][$station['StationName']] = ['toilets' => []];
    $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['Id'].'/Around'), true)['Data'];
    foreach($stationInfo['StationFacilties'] as $facility) {
        if($facility['SubCategory'] == 104) {
            foreach(explode('，', $facility['Description']) as $toilet) {
                if($toilet){
                $toiletInfo['ningbo'][$station['StationName']]['toilets'][] = [
                    'title' => '卫生间',
                    'content' => $toilet,
                ];}
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['ningbo']).' 条数据');
