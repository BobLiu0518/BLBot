<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['zhengzhou'] = [];
$citiesMeta['zhengzhou'] = [
    'name' => '郑州地铁',
    'support' => true,
    'source' => '郑州地铁官方网站、商易行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#C5820A',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_zhengzhou.svg',
];

// Get lines from official website
$lines = json_decode(file_get_contents('https://api.zzmetro.com/api/stations'), true)['data'];
$stationPage = 'https://www.zzmetro.com/lines/query/station/zid/';

// Get stations from official website
foreach($lines['stations'] as $lineId => $stations) {
    if($lineId == '9') $lineName = '城郊线';
    else if($lineId == '17') $lineName = '郑许线';
    else $lineName = $lineId.'号线';
    foreach($stations as $stationId => $station) {
        if(!in_array($station, ['郑州火车站', '郑州东站', '郑州西站', '许昌东站', '郑州航空港站'])) {
            $station = preg_replace('/站$/', '', $station);
        }
        if(!array_key_exists($station, $toiletInfo['zhengzhou'])) $toiletInfo['zhengzhou'][$station] = ['toilets' => []];
        $html = file_get_contents($stationPage.$stationId);
        if(preg_match('/<span class="pad03 w170">卫生间<\/span> <p>所在位置：<em>((.|\n)*?)<\/em><\/p>/', $html, $match) && $match[1]) {
            foreach(explode(',', str_replace("\n", ',', $match[1])) as $position) {
                if(!$position) {
                    continue;
                }
                $exist = false;
                foreach($toiletInfo['zhengzhou'][$station]['toilets'] as $id => $toilet) {
                    if($toilet['content'] == $position) {
                        $toiletInfo['zhengzhou'][$station]['toilets'][$id]['title'] = '卫生间';
                        $exist = true;
                        break;
                    }
                }
                if(!$exist) {
                    $toiletInfo['zhengzhou'][$station]['toilets'][] = [
                        'title' => $lineName,
                        'content' => $position,
                    ];
                }
            }
        }
    }
}

// Get lines from Shangyixing App
$lines = json_decode(file_get_contents('https://zzp.cnzhiyuanhui.com/api/v2/stations'), true)['content']['list'];
$stationApi = 'https://zzp.cnzhiyuanhui.com/api/stations/';

// Get stations from Shangyixing App
foreach($lines as $line) {
    foreach($line['stations'] as $station) {
        if(!in_array($station['stationName'], ['郑州火车站', '郑州东站', '郑州西站', '许昌东站', '郑州航空港站'])) {
            $station['stationName'] = preg_replace('/站$/', '', $station['stationName']);
        }
        if(!array_key_exists($station['stationName'], $toiletInfo['zhengzhou'])) $toiletInfo['zhengzhou'][$station['stationName']] = ['toilets' => []];
        $stationData = json_decode(file_get_contents($stationApi.$station['id'].'/profile'), true)['content']['station'];
        foreach(array_unique(explode('、', $stationData['toilet'] ?? '')) as $toilet) {
            if(!$toilet) {
                continue;
            }
            $toiletInfo['zhengzhou'][$station['stationName']]['toilets'][] = [
                'title' => $stationData['lineName'],
                'content' => $toilet,
            ];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['zhengzhou']).' 条数据');