<?php

global $Event;

$type = getData("so/".$Event["user_id"]);
if(!$type)
	loadModule("so.163");
else
	loadModule("so.".$type);

?>
