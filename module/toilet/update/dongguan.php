<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['dongguan'] = [];
$citiesMeta['dongguan'] = [
    'name' => '东莞轨道交通',
    'support' => true,
    'source' => '东莞轨道交通微信公众号文章',
    'time' => time(),
    'color' => [
        'main' => '#78C123',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_dongguan.svg',
];

// Link to Weixin article
$links = [
    'https://mp.weixin.qq.com/s/uW9dd-HZtaZYygJkaRydMA',
];

// Get data
foreach($links as $link) {
    $toiletsInfo = file_get_contents($link);
    preg_match('/var oriCreateTime = \'(\d+)\';/', $toiletsInfo, $match);
    $time = intval($match[1]);
    $citiesMeta['dongguan']['time'] = min($time, $citiesMeta['dongguan']['time']);
    preg_match_all('/<tr>(.+?)<\/tr>/u', $toiletsInfo, $rowMatch);
    foreach($rowMatch[1] as $row) {
        preg_match_all('/<td.*?><span.*?>(.+?)<\/span><\/td>/u', $row, $cellMatch);
        $station = '';
        $position = '';
        foreach($cellMatch[1] as $id => $cell) {
            if(preg_match('/<strong>/u', $cell)) break;
            if($id == 0) {
                if(!preg_match('/^(.+)火车站$/u', $cell)) {
                    $station = preg_replace('/站$/u', '', $cell);
                } else {
                    $station = $cell;
                }
            } else if($id == 1) {
                $position = $cell;
            } else if($station) {
                if(!array_key_exists($station, $toiletInfo['dongguan'])) {
                    $toiletInfo['dongguan'][$station] = ['toilets' => []];
                }
                $toiletInfo['dongguan'][$station]['toilets'][] = [
                    'title' => $position,
                    'content' => $cell,
                ];
            }
        }
    }
}
$citiesMeta['dongguan']['time'] = date('Y/m/d', $citiesMeta['dongguan']['time']);

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['dongguan']).' 条数据（文章更新时间 '.$citiesMeta['dongguan']['time'].'）');