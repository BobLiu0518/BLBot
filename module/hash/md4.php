l<?php

	global $Queue, $Text;
	while($nextArg = nextArg())
		$Text = $nextArg.$Text;
	if(strpos($Text, "[CQ:") !== false)leave("非法内容！");
	$Queue[]= sendBack('md4('.$Text.')='.hash('md4',$Text));

?>
