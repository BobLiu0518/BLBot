<?php

global $Command;
if(count($Command) - 1 <= 1){
	loadModule('alias.check');
}else{
	loadModule('alias.set');
}

?>
