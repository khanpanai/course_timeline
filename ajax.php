<?php

global $DB, $USER, $CFG, $OUTPUT;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');
require_once($CFG->dirroot.'/blocks/course_timeline/lib/timestamp.php');
require_once($CFG->dirroot.'/blocks/course_timeline/lib/timeline.php');

require_login();



$array = array();
$action  = required_param('action', PARAM_ALPHANUMEXT);

switch ($action) {
    case "timeline":
        $courseId = required_param('courseId', PARAM_INT);
        $sectionId = required_param('sectionId', PARAM_INT);

        $pages = get_modules_by_name("page", $courseId, $sectionId);
        $lessons = get_modules_by_name("lesson", $courseId, $sectionId);
        $modules = array_merge($pages, $lessons);


        foreach ($modules as $key=>$field) {
            $params = array();

            $availability = new timeline_availability($field->availability);

            $field->timestamp = $availability->timestamp;
            $field->active = $availability->active;
            if ($field->modname == "lesson") {

                $l = lesson::load($field->modid);
                $field->timestamp = $l->available;
                $field->active = time() > $l->available;
            }

        }

        $r = _group_by($modules, "timestamp");

        $steparr = array();

        foreach ($r as $key=>$field) {

            $a = (array)$field;

            $steparr[$key] = array();

            foreach ($a as $b) {
                $obj = new stdClass();

                $obj->type = $b->modname;
                $obj->id = $b->modid;
                $obj->name = $b->name;
                $obj->timestamp = $b->timestamp;
                $obj->active = $b->active;
                $obj->link = "/mod/$b->modname/view.php?" . "id=$b->id";
                $steparr[$key][] = $obj;
            }
        }

        $ind = 1;
        $text = "";
        ksort($steparr);
        foreach ($steparr as $key=>$step) {
            $opt = array();
            $opt["num"] = $ind;
            $opt["modules"] = $step;

            if (time()>intval($key)) {
                $opt["active"] = true;
            }

            $text = $text.$OUTPUT->render_from_template("block_course_timeline/step", $opt);
            $ind++;
        }

        echo $text;
        break;
    case "end":
        $secPerWeek = 604800;
        $sectionId = required_param('sectionId', PARAM_INT);
        $courseId = required_param('courseId', PARAM_INT);
        $lessonMax = get_max_timestamp_from_lesson($courseId, $sectionId);
        $courseModuleMax =  get_max_timestamp_from_course_module($courseId, $sectionId);

        $max = max($lessonMax, $courseModuleMax);

        $date = new DateTime();
        $date->setTimestamp(intval($max + $secPerWeek));

        echo $date->format("d.m.y");
        break;

    case "get_modules":
        $courseId = required_param('courseId', PARAM_INT);

        $query = "SELECT id, name from {course_sections} where course = ?";
        $sectionList = $DB->get_records_sql($query, array($courseId));

        $res = [];
        foreach ($sectionList as $section) {
            $obj = new stdClass();
            $obj->value = $section->id;
            $obj->name = $section->name;
            $res[] = $obj;
        }

        echo $OUTPUT->render_from_template("block_course_timeline/module_select", ["options"=>$res]);
        break;
}
