<?php

function getVerse() {
    static $data;
    if(!$data) $data = explode("\n", getData('poem/poem.txt'));
    return array_splice($data, array_rand($data), 1)[0];
}