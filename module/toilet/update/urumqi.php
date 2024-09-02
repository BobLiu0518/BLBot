<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['urumqi'] = [];
$citiesMeta['urumqi'] = [
    'name' => '乌鲁木齐地铁',
    'support' => true,
    'source' => 'Metro丝路行 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#004E8D',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_urumqi.svg',
];

// 这就不得不提把“branch.html?lNmae=一号线&sName=”硬编码进代码的含金量了
// 我看你们2号线开通以后会整出什么活来
$stations = ['国际机场站', '大地窝堡站', '宣仁墩站', '三工站', '迎宾路口站', '植物园站', '体育中心站', '铁路局站', '小西沟站', '中营工站', '新疆图书馆站', '八楼站', '王家梁站', '南湖北路站', '南湖广场站', '新兴街站', '北门站', '南门站', '二道桥站', '新疆大学站', '三屯碑站'];
$stationInfoApi = 'http://metro.shenghuochuo.com/others/v1/branch';

// Get stations
foreach($stations as $station) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode([
                'lineName' => '一号线',
                'standName' => $station,
            ]),
        ],
    ]);
    $stationInfo = json_decode(file_get_contents($stationInfoApi, false, $context), true)['result']['content'];
    $station = preg_replace('/站$/', '', $station);
    $toiletInfo['urumqi'][$station] = ['toilets' => []];
    preg_match('/<p><strong>卫生间：<\/strong><\/p><p>(.+?)<\/p>/', $stationInfo, $match);
    if($match[1]) {
        $toiletInfo['urumqi'][$station]['toilets'][] = [
            'title' => '卫生间',
            'content' => $match[1],
        ];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['urumqi']).' 条数据');