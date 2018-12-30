<?php

global $Queue;
loadModule('ban.tools');
requireSeniorAdmin();

loadBanList();saveBanList();

?>