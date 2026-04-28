<?php

global $Event, $Message, $Command;
requireLvl(3);
loadModule('weather.tools');

if($Command[0] == 'middleWare/weather') {
    $place = trim($Message);
} else {
    $place = nextArg(true);
}
if (!$place) $place = getData('weather/user/' . $Event['user_id']);
if (!$place) replyAndLeave('不知道你想查询什么地方呢…');

['fullName' => $fullName, 'lonlat' => $lonlat] = searchPoi($place);

$appKey = Config('CAIYUN_APP_KEY');
$weather = json_decode(file_get_contents("https://api.caiyunapp.com/v2.6/{$appKey}/{$lonlat}/forecast?alert=true"), true);

$sky = $weather['result']['hourly']['description'];
$rain = $weather['result']['minutely']['description'];
$temp = $weather['result']['hourly']['temperature'][0]['value'];
$feels = $weather['result']['hourly']['apparent_temperature'][0]['value'];
$humidity = $weather['result']['hourly']['humidity'][0]['value'] * 100;
$aqi = $weather['result']['hourly']['air_quality']['aqi'][0]['value']['chn'];
$alerts = implode("\n\n", array_map(
    fn($alert) => $alert['description'],
    array_filter(
        $weather['result']['alert']['content'],
        fn($alert) => intval(substr($alert['code'], -2)) >= 2
    )
));

replyAndLeave(trim("{$fullName}天气：\n{$sky}。\n当前气温{$temp}°C（体感{$feels}°C），湿度{$humidity}%，空气质量指数{$aqi}。\n{$rain}。\n\n{$alerts}"));
