<?php

global $Event, $Queue;
requireAdmin();
delData('rh/group/'.$Event['group_id']);
$Queue[]= replyMessage('Done.');

?>
