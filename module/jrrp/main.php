<?php

global $Event, $Queue, $CQ;
requireLvl(1);
loadModule('jrrp.tools');

function randString(array $strArr){
	return $strArr[rand(0, sizeof($strArr)-1)];
}

$jrrp = getRp($Event['user_id'], time());

if($jrrp > 100) $jrrp -= 50;
if($jrrp >= 90) $reply = randString(array("* 你充满了决心。","你种了一整个花园的四叶草吗？你拥有了好运的精髓！"));
else if($jrrp >= 70) $reply = randString(array("你身上散发着好运的气息。","幸运女神眷顾着你。"));
else if($jrrp >= 50) $reply = randString(array("你释放出一种正能量，感觉美好的事情随时可能发生。","你身上有种不寻常的温暖。"));
else if($jrrp >= 20) $reply = randString(array("你有什么心事吗？你有点不对劲。","你所到之处空气都变得阴沉沉的。"));
else $reply = randString(array("你印堂发黑。","你究竟是干了什么才会遭此不幸呢…"));

$Queue[]= replyMessage("你今天的人品是 ".$jrrp."。".$reply.(($jrrp < 50)?"（仅供娱乐，请勿当真）":""));

?>
