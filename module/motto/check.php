<?php

global $Event;
loadModule('motto.tools');

$motto = getMotto($Event['user_id']);
replyAndLeave($motto ? '当前个性签名：'.$motto : '暂未设置个性签名哦…');