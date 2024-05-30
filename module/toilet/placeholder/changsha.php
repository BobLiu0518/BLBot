<?php

requireLvl(6);
$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'header' => 'Content-Type: application/json',
		'content' => json_encode(['pageSize' => 0, 'pageNum' => 0, 'appDeviceType' => 2]),
	],
]);
$lines = json_decode(file_get_contents('https://itp.hncsmtr.com:8889/app/stationController/allstation', false, $context), true)['lines'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['长沙地铁'] = [];

foreach($lines as $line){
	foreach($line['stations'] as $station){
		$data['长沙地铁'][$station['stationName']] = "暂不支持长沙地铁车站查询\n（未找到官方卫生间位置数据）";
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('占位成功，共 '.count($data['长沙地铁']).' 条数据');

?>
