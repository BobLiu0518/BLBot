<?php

function removeChar($str){
	$charList = array(',', '，', '?', '？', '.', '。', '呢');
	foreach($charList as $char){
		$str = str_replace($char, '', $str);
	}
	return $str;
}

global $Event, $Command, $Queue;

if(mb_strlen($Event['message']) <= 32 && !preg_match('/(但|估计|大概|可能|就|不知道|也许|还是说|来着|只是|真要看|不好说|不管|到底|指的是|关键是|什么)/', $Event['message']) && preg_match("/(?:选|是)(.+?)还是(.+)/", $Event['message'], $result) && strpos($Event['message'], '你') === false && strpos($Event['message'], '我') === false){
	$Command = array('middleware-choose', removeChar($result[1]));
	foreach(explode('还是', $result[2]) as $thing)
		if(removeChar($thing))
			$Command[]= removeChar($thing);
	loadModule('choose');
	leave();
}

?>
