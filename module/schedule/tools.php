<?php

function getScheduleData($user_id) {
    static $db;
    if(!$db) $db = new BLBot\Database('schedule');
    return $db->get($user_id);
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
        throw new Exception('未设置课程表');
    }
    $week = getWeek($data['semesterStart'], $date);
    $weekday = date('N', $date);

    $courses = array_filter($data['courses'], function ($course) use ($week, $weekday) {
        return in_array($week, $course['weeks']) && $weekday == $course['day'];
    });
    return $courses;
}
