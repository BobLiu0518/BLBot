<?php

global $Queue, $Message;

$url = "http://trash.lhsr.cn/sites/feiguan/trashTypes_3/Handler/Handler.ashx?a=GET_KEYWORDS&kw=";
if(!$trash = trim(nextArg()))$trash = trim($Message);
if(!$trash)leave("没有垃圾名称！");

$text = getData("trash/".$trash);
if(!$text || nextArg() == "--flushcache"){
	$result = json_decode(file_get_contents($url.urlencode($trash)), true);
	if($result['kw_arr'] === NULL)
	$text = "找不到相关结果！";
	else{
		$text = "找到以下结果：";
		foreach($result['kw_arr'] as $kw)
			$text.="\n".$kw['Name']." 是 ".$kw['TypeKey'];
	}

	setData("trash/".$trash, $text);
}
$Queue[]= sendBack(trim($text));

?>
