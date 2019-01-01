<?php
    global $Queue, $CQ;
    
    date_default_timezone_set('Asia/Tokyo');

    $hour=(int)date('H');
    $minute=(int)date('i');
    $second=(int)date('s');

    if($minute==0 && $second<=1)
    {
        $groups = array("761082692");
        foreach($groups as $group_id){
            $CQ->sendGroupMsg($group_id, getData("time/".$hour.".txt"));
            $CQ->sendGroupMsg($group_id, CQCode::Record('base64://'.base64_encode(getData("time/".$hour.".mp3"))));
        }
    }

    date_default_timezone_set('Asia/Shanghai');
?>