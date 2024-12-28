<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['guiyang'] = [];
$citiesMeta['guiyang'] = [
    'name' => '贵阳轨道交通',
    'support' => true,
    'source' => '贵阳地铁微信公众号车站周边信息页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#1B4A90',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_guiyang_plain.svg',
];

// Get lines
$lines = json_decode(file_get_contents('https://gygds1.topterr.com/api/station/list'), true)['data'];

// Get stations
foreach($lines as $line) {
    foreach($line['children'] as $station) {
        if(array_key_exists($station['text'], $toiletInfo['guiyang'])) continue;
        $toiletInfo['guiyang'][$station['text']] = ['toilets' => []];
        $toilets = $station['fullStation']['weiShengJian'];
        if(!$toilets) {
            continue;
        } else if(preg_match_all('/\d+\.(.+)/', $toilets, $match)) {
            foreach($match[1] as $toilet) {
                $toiletInfo['guiyang'][$station['text']]['toilets'][] = [
                    'title' => '卫生间',
                    'content' => preg_replace('/(\d+\.|\n|\t)/', '', $toilet),
                ];
            }
        } else {
            $toiletInfo['guiyang'][$station['text']]['toilets'][] = [
                'title' => '卫生间',
                'content' => $toilets,
            ];
        }
    }
}

// Station name aliases
foreach(array_keys($toiletInfo['guiyang']) as $stationName) {
    if(preg_match('/^(.+?)\(.+\)$/u', $stationName, $match)) {
        if(!array_key_exists($match[1], $toiletInfo['guiyang'])) {
            $toiletInfo['guiyang'][$match[1]] = [];
        }
        $toiletInfo['guiyang'][$match[1]]['redirect'] = [$stationName];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['guiyang']).' 条数据');
