<?php

global $Command;
if(count($Command) - 1 == 0) {
    loadModule('nickname.check');
} else {
    loadModule('nickname.set');
}