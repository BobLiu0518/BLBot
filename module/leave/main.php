<?php

requireMaster();
global $CQ;
$CQ->setGroupLeave($g=nextArg());
leave("Leaving ".$g);

?>
