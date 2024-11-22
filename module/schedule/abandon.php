<?php

global $Event;
requireLvl(1);
loadModule('schedule.tools');

$abandoned = !isAbandoned($Event['user_id']);
setAbandoned($Event['user_id'], $abandoned);
replyAndLeave($abandoned ? '已设置今日翘课～' : '已取消今日翘课～');