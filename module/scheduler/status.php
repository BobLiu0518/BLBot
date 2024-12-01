<?php

global $Database;
requireLvl(6);

$db = $Database->scheduler;
$data = $db->find();
$reply = [];

foreach($data as $scheduler) {
    $lastExcuted = date('Y/m/d H:i:s', $scheduler['lastExecute']);
    $reply[] = "[{$scheduler['name']}] Last executed:\n  {$lastExcuted}";
}
replyAndLeave(implode("\n", $reply));