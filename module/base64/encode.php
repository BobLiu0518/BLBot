l<?php

	global $Queue, $Text;
	while($nextArg = nextArg())
		$Text = $nextArg.$Text;
	if(strpos($Text, "[CQ:") !== false)leave("非法内容！");
	$Queue[]= replyMessage('base64('.$Text.') = '.base64_encode($Text));

?>
