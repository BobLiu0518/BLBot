<?php

global $Message;

if(preg_match('/^签到/', $Message) || preg_match('/^簽到/', $Message)){
    loadModule('checkin');leave();
}
if(preg_match('/^签出/', $Message) || preg_match('/^簽出/', $Message)){
    loadModule('checkout');leave();
}
?>