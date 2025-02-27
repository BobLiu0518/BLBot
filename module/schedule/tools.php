<?php

date_default_timezone_set('Asia/Shanghai');

function getScheduleDb() {
    static $db;
    if(!$db) $db = new BLBot\Database('schedule');
    return $db;
}

function isAbandoned($data, $timestamp = null) {
    if(gettype($data) == 'string' || gettype($data) == 'integer') {
        $data = getScheduleData($data);
    }
    $abandoned = $data['abandoned'];
    return $abandoned && date('Y/m/d', $timestamp) == $abandoned;
}

function setAbandoned($user_id, $abandoned) {
    return getScheduleDb()->set(intval($user_id), [
        'abandoned' => $abandoned ? date('Y/m/d') : null,
    ]);
}

function getScheduleData($user_id) {
    $data = getScheduleDb()->get(intval($user_id));
    date_default_timezone_set($data['timezone'] ?? 'Asia/Shanghai');
    return $data;
}

function setScheduleData($user_id, $name, $semesterStart, $courses, $timezone = 'Asia/Shanghai') {
    global $Queue;
    usort($courses, function ($a, $b) {
        return $a['startTime'] <=> $b['startTime'];
    });
    $courseNames = [];
    foreach($courses as $course) {
        $courseNames[] = $course['name'];
    }
    $db = new BLBot\Database('schedule');
    $ret = $db->set($user_id, [
        'name' => $name ?: '未知课表',
        'semesterStart' => $semesterStart ?: 0,
        'courses' => $courses ?: [],
        'timezone' => $timezone,
    ]);
    $data = $db->get($user_id, 'note');
    $removedNotes = [];
    foreach($data['note'] as $courseName => $note) {
        if(!in_array($courseName, $courseNames)) {
            $removedNotes[] = $courseName;
            $db->remove($user_id, 'note.'.$courseName);
        }
    }
    if(count($removedNotes)) {
        $Queue[] = replyMessage('以下课程在新课程表中已不再可用，备注已移除：'.implode(" / ", $removedNotes));
    }
    return $ret;
}

function getTimezone($user_id) {
    return getScheduleData($user_id)['timezone'] ?? 'Asia/Shanghai';
}

function getTimezoneGMTOffset($timezone) {
    $offset = (new DateTime('now', new DateTimeZone($timezone)))->format('P');
    return 'GMT'.str_replace(':00', '', preg_replace('/(?<=[\+\-])0+/', '', $offset));
}

function getWeek($semesterStart, $current, $timezone = 'Asia/Shanghai') {
    if(!$semesterStart) $semesterStart = '0';
    $timezone = new DateTimeZone($timezone);
    $semesterStart = new DateTime('@'.$semesterStart);
    $semesterStart->setTimezone($timezone);
    $semesterStart->modify('Monday this week');
    $currentWeekStart = new DateTime('@'.$current);
    $currentWeekStart->setTimezone($timezone);
    $currentWeekStart->modify('Monday this week');
    return $semesterStart->diff($currentWeekStart)->days / 7 + 1;
}

function getCourses($data, $date) {
    if(gettype($data) == 'string' || gettype($data) == 'integer') {
        $data = getScheduleData($data);
    }
    if(!$data) {
        return false;
    }
    $week = getWeek($data['semesterStart'], $date, $data['timezone'] ?? 'Asia/Shanghai');
    $weekday = date('N', $date);

    $courses = array_filter($data['courses'] ?: [], function ($course) use ($week, $weekday) {
        return in_array($week, $course['weeks']) && $weekday == $course['day'];
    });
    if(count($courses)) {
        return $courses;
    }

    $courses = array_filter($data['courses'] ?: [], fn($course) => max($course['weeks']) >= $week);
    return count($courses) ? [] : false;
}
