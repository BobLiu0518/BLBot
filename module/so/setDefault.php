<?php

global $Event;

$type = nextArg();
if(!$type){
	delData("so/".$Event["user_id"]);
	leave("Reset to default successfully!");
}else{
	setData("so/".$Event["user_id"], trim($type));
	leave("Set default to ".$type." successfully!");
}

?>
