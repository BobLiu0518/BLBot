<?php

global $Message;

$rh = ["赛马","🐎","🏇","🐴","🦄"];

foreach($rh as $word)
	if($word == $Message){
    		loadModule('rh.join');leave();
	}

?>
