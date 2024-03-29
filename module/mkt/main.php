<?php

global $Queue;

$apiA = "http://wx.shmhky.com:8188/minhang/weixin/lines.do";
$apiB = "http://wx.shmhky.com:8188/minhang/weixin/stations.do?lineCode=";

$route = nextArg();
$upDown = nextArg();
if($route && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($route && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else replyAndLeave('参数错误！');

$dataA = json_decode(getData('mkt/a.json'),true);
$dataB = json_decode(getData('mkt/'.$route.'b-'.$upDown.'.json'),true);
if(!$dataA){
	$dataA = json_decode(file_get_contents($apiA), true);
	setData('mkt/a.json', json_encode($dataA));
}
foreach($dataA as $line)
	if($line['name'] == $route){
		$lineName = $line['name'];
		$lineId = $line['lineCode'];
		$startTime = $line[($upDown?'end':'start').'StationFirstTime'];
		$endTime = $line[($upDown?'end':'start').'StationEndTime'];
	}
if(!$lineId)replyAndLeave('查询失败…(1/2)');
if(!$dataB){
	$dataB = json_decode(file_get_contents($apiB.$lineId), true);
	setData('mkt/'.$route.'b-'.$upDown.'.json', json_encode($dataB));
}
if(!count($dataB['stations']))replyAndLeave('查询失败…(2/2)');

// 线路元信息
$reply = <<<EOT
线路名：{$lineName}
线路编码：{$lineId}
运营时间：{$startTime}-{$endTime}

EOT;
$reply .= $upDown?'下行 设站：':'上行 设站：';

// 线路设站
foreach($dataB['stations'][$upDown] as $n => $station){
	$reply .= "\n".($n + 1).' '.$station['name'];
}

$reply .= <<<EOT


如果需要切换上下行
请在命令最后加上上行或者下行哦
记得先加个空格
EOT;

$Queue[]= replyMessage($reply);

?>
