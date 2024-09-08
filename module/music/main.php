<?php

global $Event;
$type = getData("music/".$Event["user_id"]);
if(!$type) {
    $type = '163';
}
loadModule("music.".$type);