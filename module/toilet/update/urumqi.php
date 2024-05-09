<?php

requireLvl(6);

// 这就不得不提把“branch.html?lNmae=一号线&sName=”硬编码进代码的含金量了
// 我看你们2号线开通以后会整出什么活来
$stations = ['国际机场站', '大地窝堡站', '宣仁墩站', '三工站', '迎宾路口站', '植物园站', '体育中心站', '铁路局站', '小西沟站', '中营工站', '新疆图书馆站', '八楼站', '王家梁站', '南湖北路站', '南湖广场站', '新兴街站', '北门站', '南门站', '二道桥站', '新疆大学站', '三屯碑站'];
$stationInfoApi = 'http://metro.shenghuochuo.com/others/v1/branch';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['乌鲁木齐地铁'] = [];

foreach($stations as $station){
	$context = stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => 'Content-Type: application/json',
			'content' => json_encode([
				'lineName' => '一号线',
				'standName' => $station,
			]),
		],
	]);
	$stationInfo = json_decode(file_get_contents($stationInfoApi, false, $context), true)['result']['content'];
	$station = preg_replace('/站$/', '', $station);
	preg_match('/<p><strong>卫生间：<\/strong><\/p><p>(.+?)<\/p>/', $stationInfo, $match);
	if($match[1]){
		$data['乌鲁木齐地铁'][$station] = '［卫生间］'.$match[1];
	}else{
		$data['乌鲁木齐地铁'][$station] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['乌鲁木齐地铁']).' 条数据');

?>
