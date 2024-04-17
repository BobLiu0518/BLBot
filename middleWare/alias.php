<?php

global $Event;

if(config('alias',false) == true && $alias = json_decode(getData('alias/'.$Event['user_id'].'.json'),true)[nextArg()]){
	loadModule($alias);
	leave();
}

?>
