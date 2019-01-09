<?php

global $Queue, $CQ, $Event;
loadModule('permission.tools');
requireAdmin();
requireMaster();

/*****/$CQ->sendGroupMsg($Event['group_id'], "Debug: 加载命令成功！");

$permissionList = loadPermissionList();
/*****/$CQ->sendGroupMsg($Event['group_id'], "Debug: 加载列表成功！");



?>