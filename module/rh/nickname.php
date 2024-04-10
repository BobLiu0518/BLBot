<?php

requireLvl(1);

global $Event;
$char = nextArg();

if(!$char){
	delData('rh/user/'.$Event['user_id']);
	replyAndLeave('已删除昵称~');
}else{
	$code = mb_ord($char, 'UTF-8');
	if(mb_strlen($char, 'UTF-8') != 1){
		replyAndLeave('昵称只能设置单个 CJK 字符（包括但不限于汉字）噢~');
	}else if($code >= 0x3400 && $code <= 0x9FBF || $code >= 0xF900 && $code <= 0xFAFF || $code >= 0x20000 && $code <= 0x2FA1F){
		$data = ['nickname' => $char];
		setData('rh/user/'.$Event['user_id'], json_encode($data));
		replyAndLeave('成功设置昵称为：「'.$char.'」');
	}else{
		replyAndLeave('昵称只能设置单个 CJK 字符（包括但不限于汉字）噢~');
	}
}


?>
