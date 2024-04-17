<?php

global $Message;

if(preg_match('/^.+[=＝]$/', $Message)){
	loadModule('calc');
	leave();
}

?>
