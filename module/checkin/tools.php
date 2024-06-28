<?php

loadModule('credit.tools');
loadModule('exp.tools');
loadModule('jrrp.tools');

if (!function_exists('randString')) {
    function randString(array $strArr)
    {
        return $strArr[rand(0, sizeof($strArr) - 1)];
    }
}

function checkin($from)
{
    global $Event;
    $data = getCheckinData($from);
    $message = '';
            $data['status'] = 'saucer';
            $data['end'] = date('Ymd', time() + 86400); // 1 day
            $message = "你被外星人抓走了，无法行动 1 天。";
            break;
    setCheckinData($from, $data);
    return $message;
}

function getCheckinData($user_id)
{
    global $Queue, $Event;
    $file = getData('attack/user/' . $user_id);
    $data = json_decode($file ? $file : '{"status":"free","end":"0","count":{"date":"0","times":0}}', true);

    if ($Event['user_id'] == $user_id && $data['status'] != 'free' && intval($data['end']) <= intval(date('Ymd'))) {
        switch ($data['status']) {
            case 'saucer':
                $message = '你被外星人送回来了，可以自由行动了。';
                break;
        }
        $Queue[] = replyMessage($message);
        $data['status'] = 'free';
        $data['end'] = '0';
        setCheckinData($user_id, $data);
    }

    if ($data['count']['date'] < date('Ymd')) {
        $data['count']['date'] = date('Ymd');
        $data['count']['times'] = 0;
        setCheckinData($user_id, $data);
    }

    return $data;
}

function setCheckinData($user_id, $data)
{
    setData('attack/user/' . $user_id, json_encode($data));
}

function getStatus($user_id)
{
    // free / saucer
    return getCheckinData($user_id)['status'];
}

function getStatusEndTime($user_id)
{
    $time = getCheckinData($user_id)['end'];
    if ($time > 29991231) return '∞';
    return substr_replace(substr_replace($time, '/', 6, 0), '/', 4, 0);
}
?>
