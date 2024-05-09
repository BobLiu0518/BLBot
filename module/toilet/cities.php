<?php

global $Queue;
$img = trim(sendImg(getImg('toilet.png')));
if($img && !nextArg()){
	$Queue[]= replyMessage($img);
}else{
	$data = json_decode(getData('toilet/data.json'), true);
	$Queue[]= replyMessage("当前支持查询的城市：\n".implode('、', array_keys($data)).'（按接入顺序排序）');
}

?>
