<?php

global $Message;

if(strrchr($Message, "=") == "=" || strrchr($Message, "＝") == "＝"){
	loadModule('calc');
	leave();
}

?>
