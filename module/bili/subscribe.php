<?php

global $CQ, $Event;

if(!fromGroup()) replyAndLeave("此功能暂时只能在群聊中使用OvO");
$uid = ltrim(nextArg(), 'uidUID:');
$spaceApi = "https://api.bilibili.com/x/space/acc/info?mid=";
$dynamicApi = "https://api.bilibili.com/x/polymer/web-dynamic/v1/feed/space?host_mid=";
$file = json_decode(getData('bili/subscription/config/'.$Event['group_id']), true);

if(!$uid){
	if(!$file || !count($file['sub'])) replyAndLeave("群内没有订阅的up哦～");
	else {
		$reply = "群内订阅的up有：";
		foreach($file['sub'] as $sub){
			$name = json_decode(file_get_contents($spaceApi.$sub), true)['data']['name'];
			$reply .= "\n".$name.' (uid'.$sub.')';
		}
		replyAndLeave($reply);
	}
} else {
	if($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->role == 'member')
		replyAndLeave("只支持群管理使用哦～");
	if(!is_numeric($uid)) replyAndLeave("请填写要订阅up的uid哦…");

	if(!$file) $file = array("sub" => array());

	if(count($file['sub']) >= 1){
		requireLvl(2, '订阅更多 UP 主', '使用 #bili.unsubscribe 删掉一些');
		if(count($file['sub']) >= 2){
			requireLvl(3, '订阅更多 UP 主', '使用 #bili.unsubscribe 删掉一些');
			if(count($file['sub']) >= 3){
				requireLvl(4, '订阅更多 UP 主', '使用 #bili.unsubscribe 删掉一些');
			}
		}
	}
//	if(count($file['sub']) >= 3) replyAndLeave("目前群内最多订阅3位up哦～\n如果确实有需求订阅更多up，请使用 #feedback 联系 Bot 管理～");
	if(in_array($uid, $file['sub'])) replyAndLeave("已经订阅过这位up了～");
	$file['sub'][] = $uid;
	setData('bili/subscription/config/'.$Event['group_id'], json_encode($file));
	$name = json_decode(file_get_contents($spaceApi.$uid), true)['data']['name'];
	replyAndLeave('订阅 '.$name.' (uid'.$uid.') 成功！');
}

?>
