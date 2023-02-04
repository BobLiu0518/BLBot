<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	$alias = nextArg();
	if($alias !== NULL){
		$alias = ltrim($alias, '#');
		delAlias($Event['user_id'], $alias);
		$Queue[]= replyMessage('删除别名 #'.$alias.' 成功～');
		$Queue[]= sendMaster($Event['user_id'].' 删除了别名 #'.$alias);
	}else
		$Queue[]= replyMessage('不知道你打算删什么别名呢…');

?>
