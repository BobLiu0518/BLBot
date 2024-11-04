<?php

global $Command;

if(count($Command) - 1 == 1) {
    loadModule('permission.check');
} else {
    loadModule('permission.set');
}