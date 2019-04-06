<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	delAlias($Event['user_id'], nextArg());
	$Queue[]= sendBack('删除别名成功！');

?>
