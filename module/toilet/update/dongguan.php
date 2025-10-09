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

$context = stream_context_create([
	'http' => [
		'method' => 'GET',
		'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
	],
]);

// Get data
foreach($links as $link) {
    $toiletsInfo = file_get_contents($link, false, $context);
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

// Handle metro & intercity railway interchange
$toiletInfo['dongguan']['西平西'] = ['redirect' => ['西平']];

// Handle Hongfu Road station name change (temporary)
$toiletInfo['dongguan']['市民中心'] = $toiletInfo['dongguan']['鸿福路'];
$toiletInfo['dongguan']['鸿福路'] = ['redirect' => ['市民中心']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['dongguan']).' 条数据（文章更新时间 '.$citiesMeta['dongguan']['time'].'）');
