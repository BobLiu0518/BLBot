<?php

global $Queue, $Text;

$api = "https://qq.papapoi.com/ibxdhw?s=";

do{
	$nextArg = nextArg();
	$text .= $nextArg;
}while($nextArg);
$text .= $Text;
if(!$text)leave("没有文字！");

$result = file_get_contents($api.urlencode($text));
$Queue[]= sendBack($result);

?>
