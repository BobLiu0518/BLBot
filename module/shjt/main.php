<?php

global $Queue;

$apiA = "http://www.shjt.org.cn:8005/bus/TrafficLineXML.aspx?TypeID=1&name=";
$apiB = "http://www.shjt.org.cn:8005/bus/TrafficLineXML.aspx?TypeID=2&lineid=0&name=";

$route = nextArg();
$upDown = nextArg();
if($route && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($route && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else leave('参数错误！');

$dataA = json_decode(getData('shjt/'.$route.'a.json'),true);
$dataB = json_decode(getData('shjt/'.$route.'b.json'),true);
if(!$dataA){
	$dataA = json_decode(json_encode(simplexml_load_string(file_get_contents($apiA.urlencode($route)))), true);
	setData('shjt/'.$route.'a.json', json_encode($dataA));
}
if(!$dataA)leave("查询A失败！");
if(!$dataB){
	$dataB = json_decode(json_encode(simplexml_load_string(file_get_contents($apiB.urlencode($route)))), true);
	setData('shjt/'.$route.'b.json', json_encode($dataB));
}
if(!$dataB)leave('查询B失败！');

// 线路元信息
$reply = <<<EOT
线路名：{$dataA['line_name']}
线路编码：{$dataA['line_id']}

EOT;
$reply .= '运营时间：'.trim($dataA[($upDown?'end':'start').'_earlytime']).'-'.trim($dataA[($upDown?'end':'start').'_latetime'])."\n\n";
$reply .= $upDown?'下行 设站：':'上行 设站：';

// 线路设站
foreach($dataB['lineResults'.$upDown]['stop'] as $station)
		$reply .= "\n".$station['id'].' '.$station['zdmc'];

$reply .= <<<EOT


如果需要切换上下行，
请在命令最后加上上行或者下行！

相关命令：
久事公交 #jst  松江公交 #sjwgj
浦东公交 #pjt  嘉定公交 #jjt
闵行客运 #mkt
EOT;

$Queue[]= sendBack($reply);

?>
