<?php

global $Message;

if(preg_match('/^\S+(=|＝)$/', $Message)){
	loadModule('calc');
	leave();
}

?>
