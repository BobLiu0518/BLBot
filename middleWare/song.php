<?php

global $Message, $Text;

if(preg_match('/点歌/', $Message) || preg_match('/我想听/', $Message)){
    $Text = trim(str_replace("点歌","",str_replace("我想听","",$Message)));
    loadModule('song');
}

?>