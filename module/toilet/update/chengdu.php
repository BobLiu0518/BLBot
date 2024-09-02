<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['chengdu'] = [];
$citiesMeta['chengdu'] = [
    'name' => '成都地铁',
    'support' => true,
    'source' => '成都地铁 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#0079C8',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_chengdu.svg',
];

// Get lines
$stationInfoApi = 'https://cdmetro.cnzhiyuanhui.com/op/stations/';
$lines = json_decode(file_get_contents($stationInfoApi), true)['data'];

// Get stations data
foreach($lines['list'] as $line) {
    foreach($line['subLine'] as $subLine) {
        foreach($subLine['stationList'] as $station) {
            $station['stationName'] = preg_replace('/[（\(].+[\)）]$/u', '', $station['stationName']);
            if(array_key_exists($station['stationName'], $toiletInfo['chengdu'])) continue;
            $toiletInfo['chengdu'][$station['stationName']] = ['toilets' => []];
            $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['stationNo']), true)['data'];
            foreach($stationInfo['facilities']['stationFacilities'] as $facility) {
                if($facility['type'] == 'TOILET') {
                    $title = null;
                    foreach(preg_split('/\n|\r|；/u', $facility['description']) as $toilet) {
                        if(preg_match('/^(.+)：(.+)$/u', $toilet, $match)) {
                            $title = $match[1];
                            $content = $match[2];
                        } else {
                            $content = $toilet;
                        }
                        if(!$content) continue;
                        $toiletInfo['chengdu'][$station['stationName']]['toilets'][] = [
                            'title' => $title,
                            'content' => $content,
                        ];
                    }
                }
            }
        }
    }
}

// Out-of-station transfer
$toiletInfo['chengdu']['双流机场2航站楼']['redirect'] = ['双流机场2航站楼东'];
$toiletInfo['chengdu']['双流机场2航站楼东']['redirect'] = ['双流机场2航站楼'];

// Station name alias
$toiletInfo['chengdu']['中医大·省医院'] = ['redirect' => '中医大省医院'];
$toiletInfo['chengdu']['中医药大学·省人民医院'] = ['redirect' => '中医大省医院'];
$toiletInfo['chengdu']['交大兴业北街'] = ['redirect' => '兴业北街'];
$toiletInfo['chengdu']['电子科大建设北路'] = ['redirect' => '建设北路'];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['chengdu']).' 条数据');