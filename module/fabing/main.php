<?php

// https://github.com/vikiboss/60s

global $Event;

loadModule('nickname.tools');
requireLvl(1);

$target = nextArg(true);
if(!$target) $target = getNickname($Event['user_id']);
replyAndLeave(file_get_contents('http://127.0.0.1:4399/v2/fabing?encoding=text&name='.urlencode($target)));
