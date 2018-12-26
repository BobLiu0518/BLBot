<?php

global $Queue, $CQ;

$coolQVersion = $CQ->getVersionInfo();
$coolQVersion->coolq_edition = ucfirst($coolQVersion->coolq_edition);
$ip = file_get_contents('ipv4.icanhazip.com');
$ipv6 = file_get_contents('ipv6.icanhazip.com');

$Queue[]= sendBack(<<<EOT
BL1040Bot V1.0 (Based on kjBot)

以下为最近三个版本的版本记录：
V1.0
· 第一个版本

非常感谢沙雕企鹅复读机下北泽一厂
(群号761082692)群员提供的帮助！
EOT
);

?>
