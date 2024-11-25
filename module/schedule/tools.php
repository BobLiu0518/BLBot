<?php

date_default_timezone_set('Asia/Shanghai');

function getScheduleDb() {
    static $db;
    if(!$db) $db = new BLBot\Database('schedule');
    return $db;
}

function isAbandoned($user_id) {
    $abandoned = getScheduleData($user_id)['abandoned'];
    if($abandoned && date('Y/m/d') == $abandoned) {
        return true;
    }
    return false;
}

function setAbandoned($user_id, $abandoned) {
    return getScheduleDb()->set(intval($user_id), [
        'abandoned' => $abandoned ? date('Y/m/d') : null,
    ]);
}

function getScheduleData($user_id) {
    return getScheduleDb()->get(intval($user_id));
}

function setScheduleData($user_id, $name, $semesterStart, $courses) {
    usort($courses, function ($a, $b) {
        return $a['startTime'] <=> $b['startTime'];
    });

    $db = new BLBot\Database('schedule');
    return $db->set($user_id, [
        'name' => $name,
        'semesterStart' => $semesterStart,
        'courses' => $courses,
    ]);
}

function getWeek($semesterStart, $current) {
    $timezone = new DateTimeZone('Asia/Shanghai');
    $semesterStart = new DateTime('@'.$semesterStart);
    $semesterStart->setTimezone($timezone);
    $semesterStart->modify('Monday this week');
    $currentWeekStart = new DateTime('@'.$current);
    $semesterStart->setTimezone($timezone);
    $currentWeekStart->modify('Monday this week');
    return $semesterStart->diff($currentWeekStart)->days / 7 + 1;
}

function getCourses($user_id, $date) {
    $data = getScheduleData($user_id);
    if(!$data) {
        return false;
    }
    $week = getWeek($data['semesterStart'], $date);
    $weekday = date('N', $date);

    $courses = array_filter($data['courses'], function ($course) use ($week, $weekday) {
        return in_array($week, $course['weeks']) && $weekday == $course['day'];
    });
    return $courses;
}
