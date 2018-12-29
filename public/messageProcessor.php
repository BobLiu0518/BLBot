<?php

        $CQ->sendGroupMsg($group_id, getData("time/".$hour.".txt"));
if(preg_match('/^('.config('prefix', '#').')/', $Event['message'], $prefix) || preg_match('/^('.config('prefix2', '＃').')/', $Event['message'], $prefix)){
    $length = strpos($Event['message'], "\r");
    if(false===$length)$length=strlen($Event['message']);
    $Command = parseCommand(substr($Event['message'], strlen($prefix[1])-1, $length));
    $Text = substr($Event['message'], $length+2);
    $module = substr(nextArg(), strlen($prefix[1]));
    try{
        if(config('alias',false) == true && $alias = json_decode(getData('alias/'.$Event['user_id'].'/alias.json'),true)[$module])
        {
            $Queue[]= sendBack("alias: redirect to ".$alias);
            loadModule($alias);
        }
        else
            loadModule($module);
    }catch(\Exception $e){
        throw $e;
    }
}else{ //不是命令
    $Message = $Event['message'];
    require('../middleWare/Chain.php');
}

?>
