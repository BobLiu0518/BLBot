<?php

global $Queue, $Message;

$url = 'http://trash.lhsr.cn/sites/feiguan/trashTypes_3/Handler/Handler.ashx?a=GET_KEYWORDS&kw=';
$trash = nextArg(true);
if(!$trash) replyAndLeave('想查什么垃圾呢？');

if(preg_match('/\[CQ:/', $trash)) {
    replyAndLeave('要查询的物品必须是纯文本哦…');
}

$text = getData('trash/'.$trash);
if(!$text){
	$result = json_decode(file_get_contents($url.urlencode($trash)), true);
	if($result['kw_arr'] === NULL)
	$text = "Bot 好像不知道 {$trash}是什么垃圾呢…";
	else{
		$text = 'Bot 觉得：';
		foreach($result['kw_arr'] as $kw)
			$text.="\n{$kw['Name']} 是 {$kw['TypeKey']}";
	}

	setData('trash/'.$trash, $text);
}
$Queue[] = replyMessage(trim($text));
