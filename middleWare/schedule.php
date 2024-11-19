<?php

global $Message;

if($Message == '群友在上什么课') {
    loadModule('schedule.everyone');
    leave();
}
