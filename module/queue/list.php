<?php

global $Event, $Queue;

$queue = getData('queue/'.$Event['user_id']);
if($queue)
	$Queue[]= sendBack($queue);
else
	$Queue[]= sendBack('Nothing to show...');

?>
