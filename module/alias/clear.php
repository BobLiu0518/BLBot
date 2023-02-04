<?php

global $Event;

$confirm = nextArg();
if($confirm !== 'confirm'){
    replyAndLeave('确认清空设置的别名吗？确定请发送 #alias.clear confirm');
}
delData("alias/".$Event['user_id'].".json");
replyAndLeave('清空完毕～');

?>
