<?php

	require_once '../SDK/rcnb.php';

	global $Queue, $Text;

	$rcnb = new RCNB();
	while($nextArg = nextArg())
		$Text = $nextArg.$Text;
	if(strpos($Text, "[CQ:") !== false)leave("非法内容！");
	$Queue[]= sendBack($Text.' = rcnb('.$rcnb->decode($Text).')');

?>
