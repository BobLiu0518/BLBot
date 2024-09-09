<?php

function parseQQ($str) {
    if($str && preg_match('/\[CQ:at,qq=(\d+)(?:,name=.+)?\]/', $str, $QQ)) {
        return $QQ[1];
    } else return null;
}