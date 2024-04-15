<?php

global $Event;

$type = nextArg();
if(!$type){
	delData("song/".$Event["user_id"]);
	replyAndLeave("已重置默认音乐平台~");
}else if($type != '163' && $type != 'qq'){
	replyAndLeave('平台只能选择 163 或 qq 噢~');
}else{
	setData("song/".$Event["user_id"], trim($type));
	replyAndLeave("设置默认音乐平台为 ".$type." 成功~");
}

?>
