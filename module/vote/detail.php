<?php

requireInsider();
loadModule('vote.tools');

global $Queue, $Command, $Event;
$serial = nextArg();

$meta = getMeta($serial);

$title = $meta['title'];
$description = $meta['description'];
$owner = $meta['owner'];
$stat = $meta['status'];
$start = date("y/m/d H:i:s",$meta['start']);
$expire = date("y/m/d H:i:s",$meta['expire']);
$maxOps = $meta['max_ops'];
$ops = $meta['option'];

switch($stat){
    case 'active':$status="开放";break;
    case 'closed':$status="过期";break;
    case 'inactive':$status="关闭";break;
}

$msg=<<<EOT
投票 #{$serial}：
{$title}
{$description}
状态：{$status}
结束日期：{$expire}
EOT;

$n = 1;
foreach($ops as $option){
    $msg.=<<<EOT

选项{$n}：{$option}
EOT;
    $n++;
}

if($stat == 'active')
$msg.=<<<EOT

要参与投票，请键入：
#vote {$serial} {选项}
EOT;

$Queue[]= sendBack($msg);

//如果是投票owner那么顺便私聊返回当前结果

?>
