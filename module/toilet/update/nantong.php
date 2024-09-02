<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['nantong'] = [];
$citiesMeta['nantong'] = [
    'name' => '南通轨道交通',
    'support' => true,
    'source' => '南通轨道交通官方网站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#E50015',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_nantong.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://service.ntrailway.com/api/ntopen/ignoreGateway/line.common/treeList'), true)['data']['data'];
$lineApi = 'https://service.ntrailway.com/api/ntopen/ignoreGateway/listByParentId/';
$facilityApi = 'https://service.ntrailway.com/api/ntopen/ignoreGateway/select/siteInformation/convenienceFacility?stationId=';

// Get stations
foreach($lines as $line) {
    $stations = json_decode(file_get_contents($lineApi.$line['id']), true)['data']['data'];
    foreach($stations as $station) {
        if(!preg_match('/^(南通|汽车|火车)+(东|南|西|北)?站$/', $station['dictName'])) {
            $station['dictName'] = preg_replace('/站$/', '', $station['dictName']);
        }
        if(!array_key_exists($station['dictName'], $toiletInfo['nantong'])) {
            $toiletInfo['nantong'][$station['dictName']] = ['toilets' => []];
        }
        $facilities = json_decode(file_get_contents($facilityApi.$station['id']), true)['data'][0];
        if($facilities['restRoom']) {
            $toiletInfo['nantong'][$station['dictName']]['toilets'][] = [
                'title' => $line['dictName'],
                'content' => $facilities['restRoom'],
            ];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['nantong']).' 条数据');