l<?php

	global $Queue, $Text;
	while($nextArg = nextArg())
		$Text = $nextArg.$Text;
	if(strpos($Text, "[CQ:") !== false)leave("非法内容！");
	$Queue[]= sendBack('md2('.$Text.')='.hash('md2',$Text));

?>
