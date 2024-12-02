<?php

global $Command;

if(count($Command) - 1 == 0) {
    loadModule('schedule.everyone');
} else if(count($Command) - 1 <= 2) {
    loadModule('schedule.check');
}
