<?php

global $Message, $Event;
use Overtrue\Pinyin\Pinyin;

loadModule('randomBan.tools');
if($Message == '严查' || Pinyin::fullSentence($Message, 'none')->join(' ') == 'yan cha'){
	if(!rand(0, 2) && randomBan(5)){
		replyAndLeave(str_replace('查', mb_substr($Message, 1, 1), '好查！多查！'));
	}
	if(!function_exists('randomChoose')){
		function randomChoose($var){
			return $var[array_rand($var, 1)];
		}
	}
	replyAndLeave(str_replace('查', mb_substr($Message, 1, 1), randomChoose(['不许查！', '查查你的！', '查死你！', '你就会查吗？'])));
}

?>
