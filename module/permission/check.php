<?php

loadModule('permission.tools');
global $Event, $Queue;

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}
if(!$QQ) $QQ = $Event['user_id'];

$permissionList = loadPermissionList();

?>