<?php

function get_max_timestamp_from_course_module($sectionId)
{
    global $DB;
    $params = array();
    $courseId = get_config("block_course_timeline", "courseId");
    $params['courseid'] = $courseId;
    $params['section'] = $sectionId;
    $res = $DB->get_records_sql("SELECT cm.availability
                                        FROM {course_modules} cm
                                        WHERE cm.course = :courseid AND
                                        cm.section = :section", $params);

    $allTimestamps = [time()];

    foreach ($res as $key => $field) {
        $availability = new timeline_availability($field->availability);

        $allTimestamps[] = $availability->timestamp;
    }

    return max($allTimestamps);
}

function get_max_timestamp_from_lesson($sectionId)
{
    global $DB;
    $params = array();
    $courseId = get_config("block_course_timeline", "courseId");
    $moduleName = "lesson";
    $params['courseid'] = $courseId;
    $params['section'] = $sectionId;
    $res = $DB->get_records_sql("SELECT m.available
FROM {course_modules} cm
INNER JOIN {" . $moduleName . "} m on m.id = cm.instance
WHERE cm.course = :courseid AND
cm.section = :section", $params);

    $allTimestamps = [time()];

    foreach ($res as $field) {
        $allTimestamps[] = $field->available;
    }

    return max($allTimestamps);
}