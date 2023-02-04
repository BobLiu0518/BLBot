<?php

requireLvl(1);
global $Queue, $Event, $Command, $Text;

$message = trim(implode(' ', array_slice($Command, 1))."\n".$Text);
if(!$message)replyAndLeave("不知道你想反馈什么呢…");
$message = $Event['user_id'].(fromGroup()?("(@".$Event['group_id'].")"):'')." 的反馈:\n(".$Event['message_id'].")\n".$message;
$Queue[]= sendMaster($message);
$Queue[]= sendDevGroup($message);
$Queue[]= replyMessage("已经收到你的反馈啦。也请注意，如滥用反馈功能，Bot 会将你拉入黑名单。");

?>
