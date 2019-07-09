<?php

global $Queue;
$url = "http://trash.lhsr.cn/sites/feiguan/trashTypes_2/TrashQuery.aspx?kw=";
if(!$trash = trim(nextArg()))leave("没有垃圾名称！");

$result = getData("trash/".$trash);
if(!$result){
	$html = file_get_contents($url.urlencode($trash));
	if(strpos($html, "干垃圾是指"))$result = $trash."是干垃圾！";
	else if(strpos($html, "湿垃圾是指"))$result = $trash."是湿垃圾！";
	else if(strpos($html, "可回收物是指"))$result = $trash."是可回收物！";
	else if(strpos($html, "有害垃圾是指"))$result = $trash."是有害垃圾！";
	else $result = "查询失败！";
	setData("trash/".$trash, $result);
}
$Queue[]= sendBack(trim($result));

?>
