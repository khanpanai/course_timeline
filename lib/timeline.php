<?php


class timeline_availability {
    public bool $active;
    public int $timestamp;
    public function __construct($availability) {
        if ($availability == null) {
            $this->active = true;
            $this->timestamp = 0;
        }

        $decodedAvailability = json_decode($availability);

        if (!is_array($decodedAvailability->c)) {
            return;
        }

        $condition = current(array_filter($decodedAvailability->c, function ($el) {
            return $el->type == "date" && ($el->d == ">" || $el->d == ">=");
        }));
        $this->active = time() > $condition->t;
        $this->timestamp = $condition->t;
    }
}

function get_modules_by_name(string $moduleName, $courseId, $sectionId) {
    global $DB;

    $params = array();
    $params['courseid'] = $courseId;
    $params['modulename'] = $moduleName;
    $params['section'] = $sectionId;
    return $DB->get_records_sql("SELECT cm.*, m.name, md.name as modname, m.id as modid
                                   FROM {course_modules} cm
                                   INNER JOIN {modules} md on md.id = cm.module
                                   INNER JOIN {".$moduleName."} m on m.id = cm.instance
                                  WHERE cm.course = :courseid AND
                                        cm.section = :section AND
                                        md.name = :modulename", $params);
}

function _group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val->$key][] = $val;
    }
    return $return;
}
