<?php

global $Event;
requireAdmin();
delData('rh/'.$Event['group_id']);
leave('Done.');

?>
