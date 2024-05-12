<?php

global $Message, $Queue;
if(!$Message) $Message = '三连';

loadModule('jrrp');
loadModule('checkin');
loadModule('mrrp');

$reply = [];
$messages = array_splice($Queue, -3, 3);
foreach($messages as $message){
	$reply[] = preg_replace('/\[CQ:reply,id=.+?\]/', '', $message->msg);
}
$Queue[] = replyMessage(implode("\n══════════\n", $reply));

?>
