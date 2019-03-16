<?php

global $Queue, $CQ;

$coolQVersion = $CQ->getVersionInfo();
$coolQVersion->coolq_edition = ucfirst($coolQVersion->coolq_edition);
$ip = file_get_contents('ipv4.icanhazip.com');
$ipv6 = file_get_contents('ipv6.icanhazip.com');

$Queue[]= sendBack(<<<EOT
BL1040Bot V1.0 (Based on kjBot V2 legacy)

新功能请见：t.me/BLBot_New_Features
EOT
);

?>
