<?php

global $Command;
if(count($Command) - 1 == 0) {
    loadModule('ddl.check');
} else {
    loadModule('ddl.add');
}