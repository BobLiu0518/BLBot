<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['dalian'] = [];
$citiesMeta['dalian'] = [
    'name' => '大连地铁',
    'support' => true,
    'source' => '大连地铁微信公众号站点查询页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#1D0085',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_dalian.svg',
];

// Get lines
$lines = simplexml_load_string(file_get_contents('https://www.dltransgrp.com/hb-air-web/html/wxgo/resource/dalian.xml'));
$stationInfoApi = 'https://www.dltransgrp.com/hb-air-api/site/ShowSite.do?siteId=';

// Get stations
foreach($lines->l as $line) {
    foreach($line->p as $node) {
        $stationName = strval($node['lb']);
        if(!mb_strlen($stationName) || array_key_exists($stationName, $toiletInfo['dalian'])) continue;
        $stationInfo = json_decode(file_get_contents($stationInfoApi.strval($node['acc'])), true)['result'];
        $toiletInfo['dalian'][$stationName] = [
            'toilets' => [
                $stationInfo['siteInfo']['toilet'] ? [
                    'title' => '卫生间',
                    'content' => str_replace('。', '', $stationInfo['siteInfo']['toilet']),
                ] : []
            ]
        ];
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['dalian']).' 条数据');