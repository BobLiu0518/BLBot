<?php

global $Queue;

date_default_timezone_set("Asia/Shanghai");

// 获取凭证
$uname = "bst";
$pwd = "bst!2017";
$appid = "sjwgj";
$roadLineDataUrl = "http://xlcx.shsjgj.com:55027/api/PredictionPlan.ashx";

$timeStr = date("YmdHis");
$uid = $uname."_".$timeStr;
$pwd = md5($uname.$timeStr.$pwd);

$lineCode = nextArg();
$upDown = nextArg();
if($lineCode && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($lineCode && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else leave('参数错误！');
$station = nextArg();
if(!is_numeric($station) || !is_numeric($lineCode))leave('参数错误！');

$requestData = '{'.
	'"linecode":"'.$lineCode.'",'.
	'"updown":"'.$upDown.'",'.
	'"levelid":"'.$station.'",'.
	'"crdtype":"baidu"}';

$postData = 'uid='.$uid.'&appid='.$appid.'&pwd='.$pwd.'&param='.$requestData;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $roadLineDataUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
$data = json_decode(curl_exec($curl),true)['result'];
curl_close($curl);

if($data['code'] < 0){
	if($data['code'] == -3)$errMsg = "失败，线路不存在、不在运营时段或没有车辆！";
	else $errMsg = $data['msg'];
	leave('查询失败['.$data['code'].']：'.$errMsg);
}

$reply = <<<EOT
实时信息：
EOT;

foreach($data['data'] as $vehicle){
	$reply .= <<<EOT

{$vehicle['VehicleId']}
EOT;
	if($vehicle['PlanTime'])
		$reply .= " 计划发车 ".$vehicle['PlanTime'];
	else
		$reply .= " 距离 ".$vehicle['LevelId']."站 / ".round($vehicle['Distance']/1000, 2)."千米，约 ".round($vehicle['Time']/60, 2)."分钟后到达";
}

$Queue[]= sendBack($reply);

?>
