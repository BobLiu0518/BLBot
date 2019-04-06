<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	$list = chkAlias($Event['user_id']);
	$w = "设置的别名：\n";
	foreach($list as $alias => $command)
		$w .= " #".$alias." => #".$command."\n";
	$Queue[]= sendBack(rtrim($w));

?>
