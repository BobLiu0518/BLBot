<?php

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
    $data = getData('schedule/'.$user_id);
    if(!$data) {
        throw new Exception('未设置课程表');
    }
    $data = json_decode($data, true);
    $week = getWeek($data['semesterStart'], $date);
    $weekday = date('N', $date);

    $courses = array_filter($data['courses'], function ($course) use ($week, $weekday) {
        return in_array($week, $course['weeks']) && $weekday == $course['day'];
    });
    return $courses;
}
