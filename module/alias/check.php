<?php

	global $Event, $CQ;
	loadModule('alias.tools');

	$target = nextArg() ?? $Event['user_id'];
	if(!is_numeric($target)){
		$target = parseQQ($target);
	}
	$targetInfo = $CQ->getGroupMemberInfo($Event['group_id'], $target);
	$atTarget = ($target == $Event['user_id']) ? '你' : '@'.($targetInfo->card ?? $targetInfo->nickname).' ';

	$list = chkAlias($target);
	if(count($list)){
		$reply = $atTarget.'设置的别名：';
		$aliases = [];
		foreach($list as $alias => $command){
			$aliases[] = [
				'alias' => $alias,
				'command' => $command,
			];
		}
		usort($aliases, function($a, $b){
			if($result = strcmp($a['command'], $b['command'])) return $result;
			return strcmp($a['alias'], $b['alias']);
		});
		foreach($aliases as $alias){
			$reply .= "\n#".$alias['alias'].' ➪ #'.$alias['command'];
		}
		replyAndLeave($reply);
	}
	else{
		replyAndLeave($atTarget.'还没有设置别名哦～');
	}

?>
