<?php

global $Event;

if(config('alias', false) == true) {
    loadModule('alias.tools');
    if($alias = getAlias($Event['user_id'])[nextArg()]) {
        loadModule($alias);
        leave();
    }
}
