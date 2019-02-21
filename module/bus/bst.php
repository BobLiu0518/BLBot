<?php

	global $Queue;
	loadModule('bus.tools');
	$api = "http://61.129.57.72:8181/Ajax/Handler.ashx?Method=station&roadline=";
	$rl = unicode_encode(nextArg());
	if(!$rl)leave('请输入线路！');
	$result = file_get_contents($api.$rl);
	

?>
