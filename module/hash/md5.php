l<?php

	global $Queue, $Text;
	while($nextArg = nextArg())
		$Text = $nextArg.$Text;
	if(strpos($Text, "[CQ:") !== false)leave("非法内容！");
	$Queue[]= sendBack('md5('.$Text.')='.md5($Text));

?>
