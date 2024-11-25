<?php

global $Command;
if(count($Command) - 1 == 0) {
    loadModule('motto.check');
} else {
    loadModule('motto.set');
}