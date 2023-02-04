<?php

global $Command, $Queue;

$choices = array();
$count = count($Command)-1;
if(!$count)replyAndLeave("没有选项哦，要选什么呢？");
else if($count == 1)replyAndLeave("只有一个选项也会犯选择困难症吗？");
for($n=1; $n <= $count; $n++){
	$choices[] = nextArg();
}
$r = rand(0, $count-1);
$Queue[]= replyMessage("Bot 觉得应该选 ".$choices[$r]."\nBot 只是随机选择，不代表任何人的立场\n滥用本功能会被 Bot 加入黑名单".(($Command[0] == 'middleware-choose')?"\n注：有可能是笨笨 Bot 误识别了消息 OvO":''));

?>
