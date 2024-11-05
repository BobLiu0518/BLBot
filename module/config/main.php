<?php

global $Event, $Command;
requireLvl(1);
loadModule('config.tools');

if(count($Command) - 1 == 0) {
    replyAndLeave(printConfig(getConfig($Event['group_id'])));
} else {
    replyAndLeave(<<<EOT
#config.mode <mode> 设置模式
#config.add <command> 添加指令
#config.remove <command> 移除指令
EOT);
}