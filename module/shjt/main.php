<?php

global $Queue;

$routeSearchApi = 'https://api.shmaas.net/traffic/v3/querytrafficline';
$routeDetailApi = 'https://api.shmaas.net/traffic/v1/querybuslineV2';

$route = nextArg();
if(!$route) replyAndLeave('？');

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => implode("\n", [
            'Content-Type: application/json',
            'X-Saic-CityCode: 310100',
        ]),
        'content' => json_encode([
            'keywords' => $route,
            'pageNo' => 1,
            'pageSize' => 1,
            'language' => 'zh-cn',
            'type' => 0,
        ]),
    ],
]);
$routes = json_decode(file_get_contents($routeSearchApi, false, $context), true)['data']['trafficStop'];
if(!count($routes) || $routes[0]['type'] != 1) replyAndLeave('未找到名为 '.$route.' 的公交线路…');

$reply = <<<EOT
线路名：{$routes[0]['lineInfo']['lineName']}
线路编码：{$routes[0]['lineInfo']['lineId']}

EOT;

foreach($routes[0]['lineInfo']['upDown'] ? [1, 0] : [1] as $direction) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\n", [
                'Content-Type: application/json',
                'X-Saic-CityCode: 310100',
            ]),
            'content' => json_encode([
                'lineId' => $routes[0]['lineInfo']['lineId'],
                'lineName' => $routes[0]['lineInfo']['lineName'],
                'language' => 'zh-cn',
                'busType' => $routes[0]['lineInfo']['lineType'],
                'stopName' => '',
                'direction' => $direction,
            ]),
        ],
    ]);
    $routeDetail = json_decode(file_get_contents($routeDetailApi, false, $context), true)['data']['busLine'];
    $reply .= "\n【".($direction ? '上行' : '下行')."】\n";
    $reply .= '运营时间：'.$routeDetail['earlyTime'].'-'.$routeDetail['lateTime']."\n";

    foreach($routeDetail['stop'] as $n => $stop) {
        $reply .= ($n + 1).' '.$stop['stopName']."\n";
    }
}

$Queue[] = replyMessage(trim($reply));