<?php

global $Queue;

$apiA = "http://116.236.123.245:18084/api/getLineInfoByName?my=&t=&linename=";
$apiB = "http://116.236.123.245:18084/api/getLine?my=&t=&lineid=";

$route = nextArg();
$upDown = nextArg();
if($route && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($route && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else leave('参数错误！');

$dataA = json_decode(getData('jjt/'.$route.'a.json'),true);
$dataB = json_decode(getData('jjt/'.$route.'b-'.$upDown.'.json'),true);
if(!$dataA){
	$dataA = json_decode(json_encode(simplexml_load_string(file_get_contents($apiA.urlencode($route)))), true);
	setData('jjt/'.$route.'a.json', json_encode($dataA));
}
if($dataA['error'])leave("查询A失败：".$dataA['error']);
if(!$dataB){
	$dataB = json_decode(json_encode(simplexml_load_string(file_get_contents($apiB.$dataA['line_id']))), true);
	setData('jjt/'.$route.'b-'.$upDown.'.json', json_encode($dataB));
}
if($dataB['error'])leave("查询B失败：".$dataB['error']);

// 线路元信息
$reply = <<<EOT
线路名：{$dataA['line_name']}
线路编码：{$dataA['line_id']}

EOT;
$reply .= '运营时间：'.$dataA[($upDown?'end':'start').'_earlytime'].'-'.$dataA[($upDown?'end':'start').'_latetime']."\n\n";
$reply .= $upDown?'下行 设站：':'上行 设站：';

// 线路设站
foreach($dataB['lineResults'.$upDown]['stop'] as $n => $station)
		$reply .= "\n".($n+1).' '.$station['zdmc'];

$reply .= <<<EOT


如果需要切换上下行，
请在命令最后加上“上行”或者“下行”！
如单环线不显示中途站，请尝试查询下行！

相关命令：
上海交通 #shjt  久事公交 #jst
松江公交 #sjwgj  浦东公交 #pjt
闵行客运 #mkt
EOT;

$Queue[]= sendBack($reply);

?>
