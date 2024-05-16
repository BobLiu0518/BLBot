<?php

	global $Queue, $Event;
	loadModule('alias.tools');
	loadModule('credit.tools');
	requireLvl(2);
	$alias = nextArg();
	$command = nextArg();
	if($alias !== NULL && $command !== NULL){
		$alias = ltrim($alias, '#');
		$command = ltrim($command, '#');
		if(strpos(preg_replace('/\\[CQ:(?:emoji|face),id=\\d*?\\]/', '啊', $alias), '[CQ:') !== false){
			replyAndLeave('别名含有不合规内容…');
		}
		if(preg_match('/^alias(\.(check|clear|delete|del|set))?$/', $alias)){
			replyAndLeave('不允许设置别名相关指令为别名哦~');
		}
		if(!checkModule($command)){
			replyAndLeave('原名指令 #'.$command.' 不存在…');
		}
		decCredit($Event['user_id'], 1800);
		setAlias($Event['user_id'], $alias, $command);
		$Queue[]= replyMessage('设置 #'.$alias.' 为 #'.$command." 的别名成功，已收取 1800 金币！\n请注意 别用违法违规词汇做别名哦\n注：别名仅对自己生效哦～");
		$Queue[]= sendMaster($Event['user_id'].' 设置了 #'.$alias.' 作为 #'.$command.' 的别名');
	}else if(strpos($Text, "[CQ:") !== false)
		$Queue[]= replyMessage("非法内容…");
	else
		$Queue[]= replyMessage('不知道你想设置什么呢…');

?>
