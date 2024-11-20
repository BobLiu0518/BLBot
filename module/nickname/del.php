<?php

global $Event;

$nickname = getData('nickname/'.$Event['user_id']);
if(!$nickname) {
    replyAndLeave('暂未设置昵称哦…');
}
delData('nickname/'.$Event['user_id']);
replyAndLeave('删除昵称成功～');