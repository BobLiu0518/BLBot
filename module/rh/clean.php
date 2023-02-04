<?php

global $Event, $Queue;
requireAdmin();
delData('rh/'.$Event['group_id']);
$Queue[]= replyMessage('Done.');

?>
