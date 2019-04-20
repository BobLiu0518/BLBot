<?php

global $CQ, $Event, $Text;
if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member')
	requireSeniorAdmin();

if(!$Text){
	delData('addGroupMsg/'.$Event['group_id'], ' '.$Text);
	leave('恢复默认成功！');
}else{
	setData('addGroupMsg/'.$Event['group_id'], ' '.$Text);
	leave('设置成功！');
}

?>
