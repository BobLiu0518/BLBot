<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	setAlias($Event['user_id'], nextArg(), nextArg());
	$Queue[]= sendBack("设置别名成功！");

?>
