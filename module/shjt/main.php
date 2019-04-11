<?php

global $Queue;

$lineName = nextArg();
$upDown = nextArg();
if($lineName && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($lineName && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else leave('参数错误！');

$xml1 = getData('shjt/'.$lineName.'-'.$upDown.'-1.xml');
if(!$xml1){
	$requestUrl1 = "http://www.shjt.org.cn:8005/bus/TrafficLineXML.aspx?TypeID=1&name=".$lineName;
	$xml1 = file_get_contents($requestUrl1);
	setData('shjt/'.$lineName.'-'.$upDown.'-1.xml', $xml1);
}
$xml1 = simplexml_load_string($xml1);
if(!$xml1)leave('线路不存在或暂不支持查询该线路！');
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

$xml2 = getData('shjt/'.$lineName.'-'.$upDown.'-2.xml');
if(!$xml2){
	$requestUrl2 = 'http://www.shjt.org.cn:8005/bus/TrafficLineXML.aspx?TypeID=2&lineid='.$lineId.'&name='.$lineName;
	$xml2 = file_get_contents($requestUrl2);
	setData('shjt/'.$lineName.'-'.$upDown.'-2.xml', $xml2);
}
$xml2 = json_decode(json_encode(simplexml_load_string($xml2)),true);
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

$reply .= <<<EOT


如果需要切换上下行，
请在命令最后加上上行或者下行！
EOT;

$Queue[]= sendBack($reply);

?>
