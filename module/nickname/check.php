<?php

global $Event;
loadModule('nickname.tools');

$motto = getNickname($Event['user_id'], null, false);
replyAndLeave($motto ? '当前昵称：'.$motto : '暂未设置昵称哦…');