<?php

global $Message, $Queue;

if(preg_match('/六学/', $Message) || preg_match('/我就想到了/', $Message) || preg_match('/文体两开花6/', $Message) || preg_match('/多多关注/', $Message) || preg_match('/弘扬中国文化/', $Message) || preg_match('/章口就莱/', $Message))
    $Queue[]= sendBack(sendImg(getData("dt/zkjl.gif")));

?>