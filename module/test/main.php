<?php

	global $Queue, $CQ;
	requireLvl(4);
	requireSeniorAdmin();

	$cqv = $CQ->getVersionInfo();
	$stat = $CQ->getStatus();
	$reply = <<<EOT
{$cqv->app_name} {$cqv->app_version}
(running on {$cqv->runtime_os}, protocol {$cqv->protocol_name})

Statistics since last reboot:
- {$stat->stat->message_received} message(s) recieved
- {$stat->stat->message_sent} message(s) sent
- {$stat->stat->lost_times} disconnection(s)
EOT;

	$Queue[]= replyMessage($reply);

?>
