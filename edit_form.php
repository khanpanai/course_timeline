<?php
// This file is part of My Progress block for Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");


/**
 * My Progress block configuration form definition
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_timeline_edit_form extends block_edit_form {

    /**
     * Form definition
     *
     * @param mixed $mform
     * @return void
     */
    public function specific_definition($mform) {

        global $USER,$DB;
        $courses = enrol_get_users_courses($USER->id, true);

        $areanames = array();                                                                                                       
        foreach ($courses as $key => $val) {                                                                          
            $areanames[$val->id] = $val->fullname;                                                                  
        }                    

        $options = array(                                                                                                           
            'multiple' => false,                                                  
            'noselectionstring' => "Select enrolled course",                                                                
        );   
        $mform->addElement("header","courseinfo", "Course");

        $mform->addElement('autocomplete', 'courseselector', "Course", $areanames, $options);

        $mform->addElement('header', 'displayinfo', get_string('termtitle', "block_course_timeline"));

        $mform->addElement('date_time_selector', 'termstart', get_string('termstart', "block_course_timeline"));
        $mform->addRule('termstart', null, 'required', null, 'client');

        $mform->addElement('date_time_selector', 'termend', get_string('termend', "block_course_timeline"));
        $mform->addRule('termend', null, 'required', null, 'client');

  
    }
}
