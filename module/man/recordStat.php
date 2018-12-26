<?php

$msg=<<<EOT
BL1040Bot 用户使用情况记录

用法：
#recordStat
阅读用户协议

#recordStat.verify
同意

#recordStat.cancel
取消同意

#recordStat.me
查看自己的使用情况

#recordStat.global
*高级命令
*需要 SA 权限
查看全局命令使用的次数。
在群内发送会截取前十名，
私聊会展示完整列表。
EOT;

leave($msg);

?>
