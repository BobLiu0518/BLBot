<?php

if(!fromGroup() && strpos($Event['message'], '[CQ:xml,') === false){
	leave('机器人正常情况下不接收私聊信息哦，使用 #help 可以查看机器人帮助～');
}

?>
