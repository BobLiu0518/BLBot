<?php

global $Event, $Queue;
if(preg_match('/^六学^/', $Event['message']) || preg_match('/^我就想到了^/', $Event['message']) || preg_match('/^文体两开花^/', $Event['message']) || preg_match('/^多多关注^/', $Event['message']) || preg_match('/^弘扬中国文化^/', $Event['message']) || preg_match('/^章口就莱^/', $Event['message']))
    $Queue[]= sendBack(sendImg(getData("dt/zkjl.gif")));

?>