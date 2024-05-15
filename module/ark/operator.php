<?php

function poolSortByTime($a, $b){
	return $a['opEndTime'] - $b['opEndTime'];
}

$poolData = json_decode(getData('ark/pool.json'), true);
usort($poolData, 'poolSortByTime');
$operatorData = json_decode(getData('ark/operator.json'), true);

$operator = nextArg();
if(!$operator){
	replyAndLeave('不知道你要查询谁的卡池呢…');
}else if(!$operatorData[$operator]){
	replyAndLeave('干员 '.$operator.' 不存在…');
}else if($operatorData[$operator]['type'] == 'other'){
	replyAndLeave('干员 '.$operator.' 无法通过寻访获取…');
}

$reply = '';
foreach($poolData as $pool){
	if(in_array($operator, $pool['operators'][$operatorData[$operator]['star']]['up'])){
		$reply .= "\n".substr_replace(substr_replace($pool['opEndTime'], '/', 6, 0), '/', 4, 0).' '.$pool['name'];
	}
}

if(!$reply){
	replyAndLeave('干员 '.$operator.' 没有参与过卡池UP');
}else{
	replyAndLeave('干员 '.$operator.' 参与UP的卡池信息：'.$reply);
}

?>
