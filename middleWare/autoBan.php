<?php

global $Event, $CQ, $Queue;

$msg = trim(str_replace(' ', '', $Event['message']));
if($Event['group_id'] == '772503459' && in_array($msg, ['哦', '哦。', '口我', '口我。', '囗我', '囗我。'])){
	$Queue[]= replyMessage('不许哦！');
	$CQ->setGroupBan($Event['group_id'], $Event['user_id'], rand(0, 100)?60:30*24*60*60);
}

?>
