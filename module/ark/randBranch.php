<?php

global $Event;

$operators = json_decode(getData('ark/operator.json'), true);
$times = intval(nextArg());

if(!$times){
	$times = 1;
}else if($times > 25){
	replyAndLeave('抽取的分支数太多啦…一次最多抽取 25 个哦');
}

$list = [];
foreach($operators as $operator){
	$list[$operator['branch']] = [
		'profession' => $operator['profession'],
		'branch' => $operator['branch'],
	];
}
shuffle($list);

if(count($list) <= $times){
	$reply = '分支一共只有 '.count($list).' 种，Bot 全列出来啦：';
	$result = $list;
}else{
	$reply = 'Bot 抽到了这些分支：';
	$result = array_slice($list, 0, $times);
}

$list = [];
foreach($result as $branchDetail){
	$list[$branchDetail['profession']][] = $branchDetail['branch'];
}
foreach($list as $profession => $branchs){
	$reply .= "\n".$profession.'：'.implode(' ', $branchs);
}

replyAndLeave($reply);

?>
