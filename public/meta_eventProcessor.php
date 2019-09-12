<?php
    global $Queue, $CQ;
    use kjBot\SDK\CQCode;

    date_default_timezone_set('Asia/Tokyo');

    $hour = (int)date('H');
    $minute = (int)date('i');
    $second = (int)date('s');

    if($minute == 0 && $second <= 1)
    {
        $groups = array(/*"761082692"*/,"967313330","314353477","496111478");
        foreach($groups as $group_id){
            $CQ->sendGroupMsg($group_id, getData("time/".$hour.".txt"));
            $CQ->sendGroupMsg($group_id, CQCode::Record('base64://'.base64_encode(getData("time/".$hour.".mp3"))));
        }
    }

    date_default_timezone_set('Asia/Shanghai');

    $hour = (int)date('H');

    if($minute >= 37 && $minute < 40 && ($hour == 10 || $hour == 22) && $second <= 1){
        $groups = array("772503459");
        foreach($groups as $group_id)
            $CQ->sendGroupMsg($group_id, '滴，滴，离发车时间还有'.(40 - $minute).'分钟');
    }

    if($minute == 40 && ($hour == 10 || $hour == 22) && $second <= 1){
        $groups = array("772503459");
        foreach($groups as $group_id)
            $CQ->sendGroupMsg($group_id, '滴，滴，请发车');
    }

    if($minute == 41 && ($hour == 10 || $hour == 22) && $second <= 1){
        $groups = array("772503459");
        foreach($groups as $group_id)
            $CQ->sendGroupMsg($group_id, '滴，滴，车辆出场，以下漏乖');
    }

    /*if(($hour==10 || $hour==22)&& $minute==40 &&($second==30 || $second==31))
    {
        $groups = array("761082692");
        foreach($groups as $group_id)
            $CQ->sendGroupMsg($group_id, CQCode::Record('base64://'.base64_encode(getData("rcd/Gota_del_Vient.mp3"))));
    }*/

?>
