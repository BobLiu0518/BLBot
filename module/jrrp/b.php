<?php

global $Event;

requireLvl(6);
loadModule('jrrp.tools');

replyAndLeave(get_jrrp($Event['user_id'], time()));

?>
