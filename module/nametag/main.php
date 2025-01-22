<?php

global $Event, $CQ;

$reply = [];
$tag = nextArg(true);

if(!fromGroup()) {
	replyAndLeave('设置群头衔功能仅能在群聊中使用哦…');
} else if(preg_match('/\[CQ:/', $tag)) {
	replyAndLeave('不支持使用 QQ 表情等特殊内容作为头衔哦…');
} else if($CQ->getGroupMemberInfo($Event['group_id'], config('bot'), true)->role != 'owner') {
	replyAndLeave('Bot 似乎不是群主，没法设置群头衔呢…');
}

if(strlen($tag) > 18) {
	$truncated = mb_convert_encoding(substr($tag, 0, 18), 'UTF-8');
	$reply[] = "头衔过长，可能会被截断为「{$truncated}」";
}

$CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id'], $tag);
if($tag) {
	$reply[] = "设置群头衔为「{$tag}」成功～";
} else {
	$reply[] = '清除群头衔成功～';
}

replyAndLeave(implode("\n", $reply));
