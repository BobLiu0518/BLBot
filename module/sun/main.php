<?php

date_default_timezone_set('Asia/Shanghai');
global $Event, $Queue;
loadModule('sun.tools');

if(!fromGroup()){
    replyAndLeave('?');
}

$member = getRandGroupMember();
$Queue[]= replyMessage('抽到了 '.'@'.$member['nickname'].' ('.$member['user_id'].')');
$Queue[]= sendBack('[CQ:poke,qq='.$member['user_id'].']');

?>
