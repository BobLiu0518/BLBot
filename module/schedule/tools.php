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
    return getScheduleDb()->get(intval($user_id));
}

function setScheduleData($user_id, $name, $semesterStart, $courses) {
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

function getWeek($semesterStart, $current) {
    if(!$semesterStart) $semesterStart = '0';
    $timezone = new DateTimeZone('Asia/Shanghai');
    $semesterStart = new DateTime('@'.$semesterStart);
    $semesterStart->setTimezone($timezone);
    $semesterStart->modify('Monday this week'); // Next Monday ?
    $currentWeekStart = new DateTime('@'.$current);
    $semesterStart->setTimezone($timezone);
    $currentWeekStart->modify('Monday this week'); // Next Monday ?
    return $semesterStart->diff($currentWeekStart)->days / 7 + 1;
}

function getCourses($data, $date) {
    if(gettype($data) == 'string' || gettype($data) == 'integer') {
        $data = getScheduleData($data);
    }
    if(!$data) {
        return false;
    }
    $week = getWeek($data['semesterStart'], $date);
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
