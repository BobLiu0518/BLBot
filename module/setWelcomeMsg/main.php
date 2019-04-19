<?php

global $CQ, $Event, $Text;
if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member')
	requireSeniorAdmin();

if(!$Text)leave('请从第二行开始输入欢迎消息！');
setData('addGroupMsg/'.$Event['group_id'], ' '.$Text);
leave('设置成功！');

?>
