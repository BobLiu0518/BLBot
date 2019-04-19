<?php

global $Event;
if(!nextArg())leave('确认清空设置的别名吗？确定请发送 #alias.clean confirm');
delData("alias/".$Event['user_id'].".json");
leave('清空完毕！');

?>
