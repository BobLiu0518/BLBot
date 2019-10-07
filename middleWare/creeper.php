<?php

global $Message, $Text;

if(preg_match('/^Creeper/', $Message) || preg_match('/^creeper/', $Message)){
	leave("awwww man");
}

?>
