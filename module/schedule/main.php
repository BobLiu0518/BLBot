<?php

global $Command;

if(count($Command) - 1 == 0) {
    loadModule('schedule.check');
} else if(count($Command) - 1 == 1) {
    loadModule('schedule.set');
}