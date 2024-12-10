<?php

global $CQ, $Event, $Text;
if(!fromGroup()) {
        replyAndLeave("群聊中才能设置入群欢迎哦？");
}

if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member') {
        replyAndLeave("只有管理员才能设置本群入群欢迎哦…");
}

delData('addGroupMsg/'.$Event['group_id']);
replyAndLeave('恢复默认入群欢迎成功～');
