<?php

requireLvl(6);

$links = [
	'https://mp.weixin.qq.com/s/uW9dd-HZtaZYygJkaRydMA',
];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['东莞轨道交通'] = [];

foreach($links as $link){
	$toiletsInfo = file_get_contents($link);
	preg_match_all('/<tr>(.+?)<\/tr>/', $toiletsInfo, $rowMatch);
	foreach($rowMatch[1] as $row){
		preg_match_all('/<td.*?><span.*?>(.+?)<\/span><\/td>/', $row, $cellMatch);
		$station = '';
		$position = '';
		$toilets = [];
		foreach($cellMatch[1] as $id => $cell){
			if(preg_match('/<strong>/', $cell)) break;
			if($id == 0){
				if(!preg_match('/^(.+)火车站$/', $cell)){
					$station = preg_replace('/站$/', '', $cell);
				}else{
					$station = $cell;
				}
			}else if($id == 1){
				$position = $cell;
			}else{
				$toilets[] = '［'.$position.'］'.$cell;
			}
		}
		if(count($toilets)){
			$data['东莞轨道交通'][$station] = implode("\n", $toilets);
		}else{
			$data['东莞轨道交通'][$station] = '无数据，该站可能无卫生间';
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['东莞轨道交通']).' 条数据');

?>
