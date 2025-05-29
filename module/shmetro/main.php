<?php

requireLvl(2);

$stations = json_decode(file_get_contents('https://m.shmetro.com/core/shmetro/mdstationinfoback_new.ashx?act=getAllStations'), true);
$stationName = nextArg();
if (!$stationName) replyAndLeave('不知道你要查询什么车站呢…');

$pics = [];
$result = '';
foreach ($stations as $station) {
  if (trim($station['value']) == $stationName) {
    $code = $station['key'];
    $cache = "shmetro/{$code}.png";
    $pic = getCache($cache);
    if(!$pic || time() > getCacheTime($cache) + 86400 * 30) {
      $pic = file_get_contents("https://service.shmetro.com/skin/zct/{$code}.jpg");
      setCache("shmetro/{$code}.png", $pic);
    }
    if (!in_array($pic, $pics)) {
      $pics[] = $pic;
      $result .= sendImg($pic);
    }
  }
}

if (!$result) {
  replyAndLeave('未找到结果…');
}
replyAndLeave("{$stationName} 站层图：\n{$result}");
