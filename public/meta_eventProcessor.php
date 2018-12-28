<?php
    global $Queue, $CQ;
    //修正时区到日本
    date_default_timezone_set('Asia/Tokyo');

    $minute=(int)date('i');
    $second=(int)date('s');

    if($minute==0 && $second==0)
    {
        $groups = array("761082692");
        foreach($groups as $group_id){
        $CQ->sendGroupMsg($group_id, CQCode::Record('base64://'.base64_encode(file_get_contents($dir."/storage/data/time/".$hour.".mp3"))));
        $CQ->sendGroupMsg($group_id, file_get_contents($dir."/storage/data/time/".$hour.".txt"));
    }
}
?>