<?php

date_default_timezone_set("Asia/Shanghai");

function getMeta(string $serial){
    $meta = json_decode(getData('vote/'.$serial.'/meta.json'),true);
    if(!$meta)leave('找不到投票 '.$serial.'，请确认投票编号！');
    $time = time();
    $expire = $meta['expire'];
    if($time > $expire){
        //如果过期了
        $meta["status"] = "closed";
        setMeta($serial,$meta);
    }
    return $meta;
}

function setMeta(string $serial, array $meta){
    $file = json_encode($meta);
    setData("vote/".$serial."/meta.json",$file);
}

?>
