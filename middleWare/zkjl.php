<?php

global $Message, $Queue;

$zkjl = array("六学","说起","我就想到了","明年年初","中美合拍","正式开机","文体两开花","弘扬中国文化","多多关注","章口就莱");

foreach($zkjl as $word)
    if(preg_match('/'.$word.'/', $Message))
        leave(sendImg(getData("dt/zkjl.gif")));

?>