<?php

global $Event;
loadModule('schedule.tools');

if(!getScheduleData($Event['user_id'])) {
    replyAndLeave('尚未导入课表数据，无法设置翘课哦，请先用 #schedule.set 导入课表数据～');
}
$abandoned = !isAbandoned($Event['user_id']);
setAbandoned($Event['user_id'], $abandoned);
replyAndLeave($abandoned ? '已设置今日翘课～' : '已取消今日翘课～');