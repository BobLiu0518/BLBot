<?php

function blockBannedWords(string $str): string {
    $words = json_decode(getData('blocker/words.json'));
    foreach($words as $regex => $replace) {
        $str = preg_replace($regex, $replace, $str);
    }
    return $str;
}