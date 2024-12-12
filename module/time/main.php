<?php

$arg = nextArg(true);

if(is_numeric($arg)) {
    replyAndLeave(date('Y/m/d H:i:s', $arg));
} else {
    replyAndLeave(strtotime($arg ?: 'now'));
}
