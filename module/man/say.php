<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 SA 权限

让机器人对特定的群/人发送消息。

用法：
#say [参数]
{
    发送内容
}

参数列表：
-escape   不翻译 CQ 码
-async    异步执行
-toGroup  {群号} 发送给群聊
-toPerson {Q号} 发送给个人
请注意，不能同时使用 -toGroup
和 -toPerson。
不指定参数时默认：
翻译CQ码、不异步执行、发送给命
令使用者或发送给命令产生的群
EOT;
$Queue[]= sendBack($msg);

?>
