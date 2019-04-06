<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	$alias = nextArg();
	if($alias !== NULL){
		delAlias($Event['user_id'], $alias);
		$Queue[]= sendBack('删除别名 #'.$alias.' 成功！');
	}else
		$Queue[]= sendBack('请输入要删除的别名！');

?>
