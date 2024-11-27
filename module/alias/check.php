<?php

global $Event, $CQ;
loadModule('alias.tools');
loadModule('nickname.tools');

$target = nextArg() ?? $Event['user_id'];
if(!is_numeric($target)) {
    $target = parseQQ($target);
}
$targetInfo = $CQ->getGroupMemberInfo($Event['group_id'], $target);
$atTarget = '@'.getNickname($target);

$list = getAlias($target);
if(!count($list)) {
    replyAndLeave($atTarget.' 还没有设置别名哦～');
}

$reply = [$atTarget.' 设置的别名：'];
$aliases = [];
foreach($list as $alias => $command) {
    $aliases[] = [
        'alias' => $alias,
        'command' => $command,
    ];
}
usort($aliases, function ($a, $b) {
    return 2 * ($a['command'] <=> $b['command']) + ($a['alias'] <=> $b['alias']);
});
foreach($aliases as $alias) {
    $reply[] = "#{$alias['alias']} ➪ #{$alias['command']}";
}
replyAndLeave(implode("\n", $reply));
