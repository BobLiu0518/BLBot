<?php

global $Event, $Queue;

if(strpos($Event['message'], '鸡') !== false && !rand(0, 4)){
	$Queue[]= replyMessage('建议改成：'.str_replace('鸡', '只因', $Event['message']));
}

?>
