<?php

global $Event;

$type = getData("song/".$Event["user_id"]);
if(!$type)
	loadModule("song.163");
else
	loadModule("song.".$type);

?>
