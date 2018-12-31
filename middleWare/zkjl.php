<?php

global $Message, $Queue;

$zkjl = array("六学","说起","我就想到了","明年年初","中美合拍","正式开机","继续扮演","文体两开花","弘扬中国文化","多多关注","章口就莱","章承恩","六小龄童","章金莱");

foreach($zkjl as $word)
    if(preg_match('/'.$word.'/', $Message))
    {
        $Queue[]= sendBack(sendImg(getData("dt/zkjl".rand(1,2).".gif")));
        break;
    }
?>