<?php

global $Queue, $Text;

$api = "https://qq.papapoi.com/ibxdhw?s=";

$text = $Text;
do{
	$nextArg = nextArg();
	$text = $nextArg.' '.$text;
}while($nextArg);
if(!$text)leave("没有文字！");

$result = file_get_contents($api.urlencode($text));
$Queue[]= sendBack($result);

?>
