<?php

global $DB, $USER, $CFG, $OUTPUT;

require_once(__DIR__ . '/../../config.php');
require_login();

$secPerWeek = 604800;
$sectionId = required_param('sectionId', PARAM_INT);

function get_max_timestamp_from_course_module($courseId, $sectionId) {
    global $DB;
    $params = array();
    $params['courseid'] = $courseId;
    $params['section'] = $sectionId;
    $res = $DB->get_records_sql("SELECT cm.availability
                                   FROM {course_modules} cm
                                  WHERE cm.course = :courseid AND
                                        cm.section = :section", $params);

    $allTimestamps = [];

    foreach ($res as $key=>$field) {
        $availability = new timeline_availability($field->availability);

        $allTimestamps[] = $availability->timestamp;
    }

    return max($allTimestamps);
}

function get_max_timestamp_from_lesson($courseId, $sectionId) {
    global $DB;
    $params = array();
    $moduleName = "lesson";
    $params['courseid'] = $courseId;
    $params['section'] = $sectionId;
    $res = $DB->get_records_sql("SELECT m.available
                                   FROM {course_modules} cm
                                   INNER JOIN {".$moduleName."} m on m.id = cm.instance
                                  WHERE cm.course = :courseid AND
                                        cm.section = :section", $params);

    $allTimestamps = [];

    foreach ($res as $field) {
        $availability = new timeline_availability($field->available);

        $allTimestamps[] = $availability->timestamp;
    }

    return max($allTimestamps);
}