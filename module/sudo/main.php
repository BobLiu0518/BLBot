<?php

global $CQ, $Queue, $Event;
if(!isSeniorAdmin())
	$Queue[]= sendBack($CQ->getStrangerInfo($Event['user_id'])->nickname.' is not in the sudoers file. This incident will be reported.');
else
	$Queue[]= sendBack('sudo: unable to resolve host BL1040Bot');

?>
