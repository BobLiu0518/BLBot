<?php

	global $Event;
	loadModule('alias.tools');
	$list = chkAlias($Event['user_id']);
	if(count($list)){
		$reply = '你设置的别名：';
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
			$reply .= "\n#".$alias['alias'].' => #'.$alias['command'];
		}
		replyAndLeave($reply);
	}
	else{
		replyAndLeave('你还没有设置别名哦～');
	}

?>
