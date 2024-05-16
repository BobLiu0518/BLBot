<?php

requireLvl(1);

global $Event;
$char = nextArg();

if(!$char){
	if($data = getData('rh/user/'.$Event['user_id'])){
		delData('rh/user/'.$Event['user_id']);
		replyAndLeave('已删除昵称「'.json_decode($data, true)['nickname'].'」~');
	}else{
		replyAndLeave('你还没有设置昵称哦，使用 #rh.nickname <昵称> 即可设置~');
	}
}else{
	$code = mb_ord($char, 'UTF-8');
	if(mb_strlen($char, 'UTF-8') == 1 && ($code >= 0x3400 && $code <= 0x9FBF || $code >= 0xF900 && $code <= 0xFAFF || $code >= 0x20000 && $code <= 0x2FA1F)){
		$data = ['nickname' => $char];
		setData('rh/user/'.$Event['user_id'], json_encode($data));
		replyAndLeave('成功设置昵称为：「'.$char.'」');
	}else{
		replyAndLeave('昵称只能设置单个汉字（或其他 CJK 字符）噢~');
	}
}


?>
