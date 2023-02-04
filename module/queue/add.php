<?php

global $Event, $Queue, $Text;

$queue = getData('queue/'.$Event['user_id']);
if($Text === NULL)leave('No content!');
$queue .= "\n".$Text;
setData('queue/'.$Event['user_id'], trim($queue));
$Queue[]= sendBack('Success!');

?>
