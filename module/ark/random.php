<?php

global $Event;

$operators = json_decode(getData('ark/operator.json'), true);
$star = nextArg();
$times = intval(nextArg());
if(!$star){
	$star = 0;
}else if($star > 6 || $star < 0){
	replyAndLeave('真的存在 '.$star.'★ 干员嘛？');
}
$stars = ($star == 0) ? '干员' : $star.'★ 干员';

if(!$times){
	$times = 1;
}else if($times > 20){
	replyAndLeave('抽取的干员数太多啦…一次最多抽取 20 名哦');
}

$list = [];
foreach($operators as $operator){
	if($star == 0 || intval($operator['star']) == $star){
		$list[]= $operator['name'];
	}
}
shuffle($list);

if(count($list) <= $times){
	$reply = $stars.'一共只有 '.count($list).' 位，Bot 全列出来啦：';
	$result = $list;
}else{
	$reply = 'Bot 抽到了这些'.$stars.'：';
	$result = array_slice($list, 0, $times);
}

$list = [];
foreach($result as $operator){
	$list[$operators[$operator]['profession']][] = $operator;
}
foreach($list as $profession => $professionList){
	$reply .= "\n".$profession.'：'.implode(' ', $professionList);
}

replyAndLeave($reply);

?>
