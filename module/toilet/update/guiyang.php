<?php

requireLvl(6);

$lines = json_decode(file_get_contents('http://gongzhongfuwu.guiyang3haoxian.cn/api/station/list'), true)['data'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['贵阳轨道交通'] = [];

foreach($lines as $line){
	foreach($line['children'] as $station){
		if($data['贵阳轨道交通'][$station['text']]) continue;;
		$toiletInfo = $station['fullStation']['weiShengJian'];
		if(!$toiletInfo){
			$data['贵阳轨道交通'][$station['text']] = '无数据，该站可能无卫生间';
		}else if(preg_match_all('/\d+\.(.+)/', $toiletInfo, $match)){
			$data['贵阳轨道交通'][$station['text']] = [];
			foreach($match[1] as $toilet){
				$data['贵阳轨道交通'][$station['text']][] = '［卫生间］'.preg_replace('/(\d+\.|\n|\t)/', '', $toilet);
			}
			$data['贵阳轨道交通'][$station['text']] = implode("\n", $data['贵阳轨道交通'][$station['text']]);
		}else{
			$data['贵阳轨道交通'][$station['text']] = '［卫生间］'.$toiletInfo;
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['贵阳轨道交通']).' 条数据');

?>
