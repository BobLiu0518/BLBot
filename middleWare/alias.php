<?php

global $Event;

$command = explode(" ", $Event['message'])[0];
if(config('alias',false) == true && $alias = json_decode(getData('alias/'.$Event['user_id'].'.json'),true)[$command]){
	//$Queue[]= sendBack("alias: redirect to ".$alias);
	loadModule($alias);
	leave();
}

?>
