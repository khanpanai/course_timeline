<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block course_timeline is defined here.
 *
 * @package     block_course_timeline
 * @copyright   2023 Ram Team roman@ram.team
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_timeline extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_course_timeline');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        global $OUTPUT, $PAGE, $DB, $USER;

        $PAGE->requires->css("/blocks/course_timeline/css/style.css");
        $PAGE->requires->js_call_amd("block_course_timeline/step", "init");
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $array = array();

        $array["options"] = $this->get_sections();
        $array["courses"] = $this->get_courses();
        $array['brand_color'] = get_config("theme_boost_union", "brandcolor");
        $this->content->text = $OUTPUT->render_from_template("block_course_timeline/default", $array);

        return $this->content;
    }


    function get_courses() {
        global $USER;
        $courseList = enrol_get_all_users_courses($USER->id);

        $res = [];
        foreach ($courseList as $course) {
            $obj = new stdClass();
            $obj->value = $course->id;
            $obj->name = $course->fullname;
            $res[] = $obj;
        }

        return $res;
    }

    function get_sections() {
        global $DB;

        $courseId = get_config("block_course_timeline", "courseId");

        $query = "SELECT id, name from {course_sections} where course = ?";
        $sectionList = $DB->get_records_sql($query, array($courseId));

        $res = [];
        foreach ($sectionList as $section) {
            $obj = new stdClass();
            $obj->value = $section->id;
            $obj->name = $section->name;
            $res[] = $obj;
        }

        return $res;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_course_timeline');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return array(
            'all' => true,
        );
    }

    function _self_test() {
        return true;
    }
}
