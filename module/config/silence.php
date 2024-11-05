<?php

global $Event;
requireLvl(1);
loadModule('config.tools');
requireGroupAdmin();

$data = getConfig($Event['group_id']);
$data['silence'] = !$data['silence'];
setConfig($Event['group_id'], $data);
replyAndLeave('已'.($data['silence'] ? '启用' : '禁用').'静默模式。');