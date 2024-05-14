<?php

requireLvl(6);

$stationsPage = file_get_contents('http://www.fzmtr.com/html/fzdt/index.html');
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['福州地铁'] = [];

preg_match_all('/<SPAN class="name">(.+?)<\/SPAN>/', $stationsPage, $match);
foreach($match[1] as $station){
	$data['福州地铁'][$station] = "暂不支持福州地铁车站查询\n（未找到官方卫生间位置数据）";
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('占位成功，共 '.count($data['福州地铁']).' 条数据');

?>
