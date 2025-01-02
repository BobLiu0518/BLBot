<?php

global $Command;

if(preg_match('/^(.+)是什么垃圾$/', $Message, $matches)) {
    $Command = ['middleware-trash', $matches[1]];
    loadModule('trash');
    leave();
}
