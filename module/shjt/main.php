<?php

global $Queue;

$lineName = nextArg();
$upDown = nextArg();
if($lineName && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($lineName && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else leave('参数错误！');

$requestUrl1 = "http://www.shjt.org.cn:8005/bus/TrafficLineXML.aspx?TypeID=1&name=".$lineName;
$xml1 = simplexml_load_string(file_get_contents($requestUrl1));
if(!$xml1)leave('暂不支持查询该线路！');
$lineId = $xml1->line_id;
if(!$upDown)$time = $xml1->start_earlytime.'-'.$xml1->start_latetime;
else $time = $xml1->end_earlytime.'-'.$xml1->end_latetime;

$upDownPrompt = $upDown?"下行":"上行";
$reply = <<<TOC
线路名：$lineName
线路编码：$lineId
运营时间：$time

$upDownPrompt 设站：
TOC;

$requestUrl2 = 'http://www.shjt.org.cn:8005/bus/TrafficLineXML.aspx?TypeID=2&lineid='.$lineId.'&name='.$lineName;
$xml2 = json_decode(json_encode(simplexml_load_string(file_get_contents($requestUrl2))),true);
if(!$upDown)
	foreach($xml2['lineResults0']['stop'] as $stop)
		$reply .= <<<TOC

{$stop['id']} {$stop['zdmc']}
TOC;
else
	foreach($xml2['lineResults1']['stop'] as $stop)
		 $reply .= <<<TOC

{$stop['id']} {$stop['zdmc']}
TOC;

$Queue[]= sendBack($reply);

?>
