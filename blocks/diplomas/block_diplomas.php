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

/**
 * Shows a list of diploma courses.
 *
 * @package   block_diplomas
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_diplomas extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_diplomas');
    }
    
    function specialization() {
        if (isset($this->config->title)) {
            $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('pluginname', 'block_diplomas');
        }
    }

    function get_content() {
        global $CFG, $OUTPUT, $PAGE, $SITE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         = new stdClass;
        $this->content->items  = array();
        $this->content->icons  = array();
        $this->content->footer = '';
        $this->content->text   = '';
        
        $courses = get_courses();
        $site = isset($SITE) ? $SITE : get_site();
        $options = array("context" => context_course::instance($site->id), "trusted" => true, "para" => false);
        
        foreach ($courses as $course) {
            $activities = get_array_of_activities($course->id);
            $has_subcourse = false;
            foreach ($activities as $activity) {
                if ($activity->mod == 'subcourse') {
                    $has_subcourse = true;
                    break;
                }
            }
            if (in_array($course->category, array(0, $PAGE->category->id)) || !$has_subcourse) continue;
            
            $text = format_text($course->fullname, FORMAT_MOODLE, $options);
            $link = new moodle_url('/course/view.php', array('id' => $course->id));
            
            $this->content->text .= html_writer::start_tag('p');
            $this->content->text .= html_writer::tag('a', $text, array('target' => '_BLANK', 'title' => $text, 'href' => $link, 'class' => 'limit-2'));
            $this->content->text .= html_writer::end_tag('p');
        }
        return $this->content;
    }

    public function applicable_formats() {
        return array('all' => true,
                     'site' => true,
                     'site-index' => true,
                     'course-view' => true, 
                     'course-view-social' => true,
                     'mod' => true, 
                     'mod-quiz' => true);
    }

    public function instance_allow_multiple() {
          return true;
    }

    function has_config() {return false;}
    public function cron() {return true;}
}
