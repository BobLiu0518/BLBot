<?php

global $Message;
use Overtrue\Pinyin\Pinyin;

if(strtolower($Message) == 'qd' || Pinyin::abbr($Message)->join('') == 'qd'){
	loadModule('checkin');
	leave();
}

?>
