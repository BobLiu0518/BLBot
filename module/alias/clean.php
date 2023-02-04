<?php

global $Event;
if(!nextArg()){
    $Queue[]= replyMessage('确认清空设置的别名吗？确定请发送 #alias.clean confirm');
    leave();
}
delData("alias/".$Event['user_id'].".json");
$Queue[]= replyMessage('清空完毕～');

?>
