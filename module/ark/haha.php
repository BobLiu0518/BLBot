<?php

global $Queue;

$host = 'https://static.prts.wiki/voice/';

$list = ['char_113_cqbw/CN_028.wav', 'char_143_ghost/CN_004.wav', 'char_4025_aprot2/CN_028.wav', 'char_140_whitew/CN_024.wav', 'char_140_whitew/CN_034.wav'];

$Queue[]= sendBack('[CQ:record,file='.$host.$list[rand(0, count($list) - 1)].']');

?>
