<?php

global $Event;

$api = "https://shanghaicity.openservice.kankanews.com/citizen/repeat?name=";
if(!$name = nextArg())leave("没有姓名！");

$result = json_decode(file_get_contents($api.urlencode($name)), true);
if(!$result) leave('查询失败！');
leave("截止今日，在上海市户籍人口中共有".$result['count']."个".$result['name']."信息。");

?>
