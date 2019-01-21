<?php
	global $Event, $CQ;
	$t = rand(1, 10*60);
	$CQ->setGroupBan($Event['group_id'], $Event['user_id'], $t);
?>
