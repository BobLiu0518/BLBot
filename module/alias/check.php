<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	$list = chkAlias($Event['user_id']);
	$w = "设置的别名：\n";
	if(count($list))
		foreach($list as $alias => $command)
			$w .= " #".$alias." => #".$command."\n";
	else
		$w = "你还没有设置别名！";
	$Queue[]= sendBack(rtrim($w));

?>
