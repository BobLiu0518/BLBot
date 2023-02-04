<?php

global $Event, $CQ;

if(!fromGroup()) replyAndLeave("此功能暂时只能在群聊中使用OvO");
if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member')
	replyAndLeave("只支持群管理使用哦～");
$file = json_decode(getData('bili/subscription/config/'.$Event['group_id']), true);
$uid = ltrim(nextArg(), 'uidUID:');
$spaceApi = "https://api.bilibili.com/x/space/acc/info?mid=";

ini_set('user_agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36');
$context = stream_context_create(['http' => ['header' => 'Cookie: SESSDATA='.getData('bili/api/sessdata')]]);

if(!$uid) replyAndLeave("不知道你想取消订阅哪位up呢…");
if(!$file || !in_array($uid, $file['sub'])) replyAndLeave("群里没有订阅这位up呢…");
$file['sub'] = array_diff($file['sub'], [$uid]);
setData('bili/subscription/config/'.$Event['group_id'], json_encode($file));
$name = json_decode(file_get_contents($spaceApi.$uid, false, $context), true)['data']['name'];
replyAndLeave('取消订阅 '.$name.' (uid'.$uid.') 成功！');

?>
