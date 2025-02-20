<?php

global $Message;

if(preg_match('/^[A-Za-z0-9\+\/]+={0,2}$/', $Message)) {
    replyAndLeave(base64_decode($Message));
}