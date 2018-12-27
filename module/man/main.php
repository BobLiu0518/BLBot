<?php

global $Queue;
if(nextArg() == "-advanced"){
$msg=<<<EOT
*这是一个高级命令列表，其中大部分需要权限。
*M 权限指 Master 权限，SA 权限指 SeniorAdmin 权限，I 权限指 Insider 权限。
*测试命令可能会不稳定，而有些的 #help 文档不完全。
*请不要滥用这些命令。
高级命令：
AD announce log restart say shutdown status test
含有高级选项的命令：
credit recordStat time
未知功能的命令：
draw
测试命令：
insider
EOT;
}else{
$msg=<<<EOT
*请仔细阅读完全部内容后再尝试命令！
*查看傻×都能看懂的帮助文档请使用 #help
说明：
在 man 信息中，大括号表示必须项，中括号表示可选项，管线符表示左右均可以，（发送命令时请注意不要加括号，并且使用半角字符）如果你的参数包含空格，可以使用英文单双引号。
发送
#man.{命令}[.下一级命令]
获得特定命令的帮助。
发送
#man -advanced
查看高级命令。
可用命令：
checkin credit issue osu pixiv recordStat roll search song sleep time trans unsleep version voice
EOT;
}
$Queue[]= sendBack($msg);

?>
