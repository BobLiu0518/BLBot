<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	$alias = nextArg();
	$command = nextArg();
	if($alias !== NULL && $command !== NULL){
		setAlias($Event['user_id'], $alias, $command);
		$Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 设置 #'.$alias.' 为 #'.$command.' 的别名成功！');
	}else if(strpos($Text, "[CQ:") !== false)
		leave("非法内容！");
	else
		$Queue[]= sendBack('请输入要设置的别名！');

?>
