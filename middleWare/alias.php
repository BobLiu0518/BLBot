<?php

global $Event, $Command, $Text;

$length = strpos($Event['message'], "\r");
if(false===$length)$length = strlen($Event['message']);
$Command = parseCommand(substr($Event['message'], strlen(Config('prefix', '#'))-1, $length));
$Text = substr($Event['message'], $length+2);

$module = substr(nextArg(), strlen(Config('prefix', '#')));
$command = explode(" ", $Event['message'])[0];
if(config('alias',false) == true && $alias = json_decode(getData('alias/'.$Event['user_id'].'.json'),true)[$command]){
	//$Queue[]= sendBack("alias: redirect to ".$alias);
	loadModule($alias);
	leave();
}

?>
