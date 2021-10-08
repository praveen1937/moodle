<?php
// This file is part of Moodle - http://moodle.org/
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

/**
 * Course activities block
 *
 * @package    block_course_activities
 * @copyright  2021 Praveen Charles <praveen1937@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_activities extends block_base {

    /**
     * Initializes the block, called by the constructor
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_course_activities');
    }

    /**
     * Amend the block instance after it is loaded
     */
    public function specialization() {
        if (strpos($this->page->pagetype, PAGE_COURSE_VIEW) === 0 && $this->page->course->id != SITEID) {
            $this->title = get_string('pluginname', 'block_course_activities');
        }
    }

    /**
     * Which page types this block may appear on
     * @return array
     */
    public function applicable_formats() {
        return array('course-view-*' => true);
    }

    /**
     * Does the block have a global settings.
     *
     * @return bool
     */
    public function has_config() {
        return false;
    }

    /**
     * Populate this block's content object
     * @return stdClass block content info
     */
    public function get_content() {
        global $CFG, $DB, $USER, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        $course = $this->page->course;

        require_once($CFG->dirroot.'/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $modules = array();

        foreach ($modinfo->cms as $cm) {
            $icon = $OUTPUT->image_icon('icon', get_string('pluginname', $cm->modname), $cm->modname);
            $url = $CFG->wwwroot.'/mod/'.$cm->modname.'/view.php?id='.$cm->id;
            $moduletype = $cm->modname;
            $modulename = $cm->name;
            $createddate = date('d-M-Y' , $cm->added);
            $completionstate = $DB->get_field('course_modules_completion' , 'completionstate' ,
                                                array('coursemoduleid' => $cm->id , 'userid' => $USER->id));
            $completionstatus = get_string('notcompleted', 'block_course_activities');
            if ($completionstate == 1) {
                $completionstatus = get_string('completed', 'block_course_activities');
            }
            $modules[] = array('cmid' => $cm->id , 'icon' => $icon , 'url' => $url , 'moduletype' => $moduletype ,
                             'modulename' => $modulename , 'createddate' => $createddate ,
                             'completionstatus' => $completionstatus);
        }

        $this->content = new stdClass();
        $data = [
            'modules' => $modules
        ];
        $this->content->text = $OUTPUT->render_from_template('block_course_activities/course_activities', $data);
        $this->content->footer = '';

        return $this->content;
    }
}
