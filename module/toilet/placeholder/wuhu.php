<?php

requireLvl(6);
$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'header' => 'Content-Type: application/x-www-form-urlencoded',
		'content' => 'action=get-all-info&start=&end=',
	],
]);
$lines = json_decode(file_get_contents('http://www.wuhurailtransit.com/Ajax/api.ashx', false, $context), true);
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['芜湖轨道交通'] = [];

foreach($lines as $line){
	foreach($line as $station){
		$data['芜湖轨道交通'][$station['Title']] = "暂不支持芜湖轨道交通车站查询\n（未找到官方卫生间位置数据）";
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('占位成功，共 '.count($data['芜湖轨道交通']).' 条数据');

?>
