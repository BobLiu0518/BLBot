<?php

global $Message;
if(date('N') == 4 && preg_match('/v.{0,5}50|KFC|五十/i', $Message) && !preg_match('/\[CQ:/', $Message)){
	loadModule('v50');
	leave();
}
