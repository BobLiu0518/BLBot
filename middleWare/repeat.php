<?php

global $Event, $Message, $Queue;

if(!preg_match('/^\[.+\]$/', preg_replace('/\[CQ:(emoji|face|emoji|at).+?\]/', '', $Message)) && !preg_match('/^(\[.+\])?\//', $Message)) {
    function parsePicId($str) {
        return preg_replace('/\[CQ:image,file=.+?fileid=(.+?)_.+?\]/', '$1', $str);
    }

    $recent = [];
    $current = -1;
    foreach(range(1, 4) as $i) {
        $msg = getData('repeat/'.$Event['group_id'].'-'.$i);
        if(!$msg && $current == -1 && $i != 4) {
            $current = $i;
        } else {
            $recent[] = parsePicId($msg);
        }
    }
    if($current != -1) {
        setData('repeat/'.$Event['group_id'].'-'.$current, $Message, true);
        setData('repeat/'.$Event['group_id'].'-'.($current % 3 + 1), '');
        setData('repeat/'.$Event['group_id'].'-4', '');

        if(parsePicId($Message) == $recent[0] && parsePicId($Message) == $recent[1] && parsePicId($Message) != $recent[2]) {
            if(coolDown('repeat/'.$Event['group_id']) > 0) {
                coolDown('repeat/'.$Event['group_id'], 60);
                $Queue[] = sendBack($Message);
            }
            setData('repeat/'.$Event['group_id'].'-4', $Message, true);
            leave();
        }
    }
}

