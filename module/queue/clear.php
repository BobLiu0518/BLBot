<?php

global $Event, $Queue;

delData('queue/'.$Event['user_id']);
$Queue[]= sendBack('Success!');

?>
