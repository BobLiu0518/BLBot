<?php

global $Message;

if(strlen($Message) >= 18 && preg_match('/^[A-Za-z0-9\+\/]+={0,2}$/', $Message) && $result = base64_decode($Message)) {
    replyAndLeave("Base64 解析结果：\n".preg_replace('/\[CQ:.+?\]/', '', $result));
}
