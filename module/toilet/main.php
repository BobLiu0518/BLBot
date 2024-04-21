<?php

global $Message;

$station = nextArg();
if(!$station) $station = $Message;
if(!$station) replyAndLeave('要查询什么车站呢？');
$data = json_decode(getData('toilet/data.json'), true);
$reply = '';
$companies = [];
foreach($data as $companyName => $company){
	if($company[$station]) $reply .= "\n\n".$companyName.' '.$station." 站：\n".$company[$station];
	$companies[] = $companyName;
}
if(!strlen($reply)){
	replyAndLeave('没有查询到名为 '.$station." 的车站哦…\n目前仅支持".implode('、', $companies).'的车站洗手间查询哦（更多城市接入中…）');
}else{
	replyAndLeave(trim($reply));
}

?>
