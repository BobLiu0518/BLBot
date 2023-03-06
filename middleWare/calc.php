<?php

global $Message;

if(strrchr($Message, "=") == "="){
	$Message = trim(rtrim($Message, '='));
	loadModule('calc');
	leave();
}

?>
