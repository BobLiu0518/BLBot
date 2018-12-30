<?php

global $Queue;
loadModule('ban.tools');
requireSeniorAdmin();

$Queue[]=sendBack(loadBanList().saveBanList());

?>