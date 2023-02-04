<?php

function removeChar($str){
	$charList = array(',', '，', '?', '？', '.', '。', '呢');
	foreach($charList as $char){
		$str = str_replace($char, '', $str);
	}
	return $str;
}

global $Event, $Command, $Queue;

if(mb_strlen($Event['message']) <= 32 && preg_match("/(?:选|是|要)(.+?)还是(.+)/", $Event['message'], $result) && strpos($Event['message'], '你') === false && strpos($Event['message'], '我') === false){
	$Command = array('middleware-choose', removeChar($result[1]));
	foreach(explode('还是', $result[2]) as $thing)
		if(removeChar($thing))
			$Command[]= removeChar($thing);
	loadModule('choose');
	leave();
}

?>
