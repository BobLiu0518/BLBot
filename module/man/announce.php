<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 SA 权限

向所有群发送广播，
会添加小尾巴，
小尾巴包括了发送者信息。

用法：
#announce
{
    广播内容
}
EOT;
$Queue[]= sendBack($msg);

?>
