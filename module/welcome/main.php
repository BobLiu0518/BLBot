<?php

global $CQ, $Event, $Text;
if(!fromGroup()){
	replyAndLeave("群聊中才能设置入群欢迎哦？");
}

$args = '';
while($nextArg = nextArg()){
	$args .= ' '.$nextArg;
}
$Text = trim($args."\n".$Text);

if(!$Text){
	replyAndLeave("当前入群欢迎：\n@新成员 ".trim(getData('addGroupMsg/'.$Event['group_id'])));
}else{
	if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member'){
		replyAndLeave("只有管理员才能设置本群入群欢迎哦…");
	}
	setData('addGroupMsg/'.$Event['group_id'], ' '.$Text);
	replyAndLeave('设置成功～');
}

?>
