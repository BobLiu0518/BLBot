<?php

global $Queue;

date_default_timezone_set("Asia/Shanghai");

// 获取凭证
$uname = "bst";
$pwd = "bst!2017";
$appid = "sjwgj";
$roadLineDataUrl = "http://xlcx.shsjgj.com:55027/api/RoadlineStation.ashx";

$timeStr = date("YmdHis");
$uid = $uname."_".$timeStr;
$pwd = md5($uname.$timeStr.$pwd);

$lineName = nextArg();
if($lineName == "松莘线B线")$lineName = "松莘B线";
$upDown = nextArg();
if($lineName && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($lineName && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else replyAndLeave('参数错误…');

//先读取缓存，如果有就不重新获取了
$data = json_decode(getData('sjwgj/'.$lineName.'-'.$upDown.'.json'),true);
if(!$data){
	// 如果没有缓存
	$requestData = '{'.
		'"roadline":"'.$lineName.'",'.
		'"updown":"'.$upDown.'",'.
		'"crdtype":"baidu"}';

	$postData = 'uid='.$uid.'&appid='.$appid.'&pwd='.$pwd.'&param='.urlencode($requestData);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $roadLineDataUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
	$data = json_decode(curl_exec($curl),true)['result'];
	curl_close($curl);

	setData('sjwgj/'.$lineName.'-'.$upDown.'.json', json_encode($data));
}

if($data['code'] < 0)replyAndLeave('查询失败['.$data['code'].']：'.$data['msg']);

// 环线类型
switch($data['data']['LineType']){
	case 'SingleLoop': $lineType = "单环线"; break;
	case 'DoubleLoop': $lineType = "双环线"; break;
	case 'Normal': $lineType = "非环线"; break;
	default: $lineType = $data['data']['LineType'];
}
$upDownPrompt = $upDown?"下行":"上行";

// 线路元信息
$reply = <<<EOT
线路名：{$data['data']['Roadline']}
线路编码：{$data['data']['LineCode']}
环线类型：$lineType
运营时间：{$data['data']['StartTime']}-{$data['data']['EndTime']}

$upDownPrompt 设站：
EOT;

// 线路设站
foreach($data['data']['lstStation'] as $station){
	$reply .= <<<EOT

{$station['LevelId']} {$station['LevelName']}
EOT;
}

$reply .= <<<EOT


如果需要切换上下行
请在命令最后加上“上行”或者“下行”哦
记得先加个空格

如果需要查询实时信息 请使用指令：
#sjwgj.rti {$data['data']['LineCode']} $upDownPrompt <站级序号>
EOT;

$Queue[]= replyMessage($reply);

?>
