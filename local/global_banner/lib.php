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

function local_global_banner_category_notification () {
    global $COURSE, $SITE;

    $site = isset($SITE) ? $SITE : get_site();

    $config = get_config("local_global_banner");
    if ($config->visible) {
        //use context_course rather then context_system because of caching
        $options = array("context" => context_course::instance($site->id), "trusted" => true, "para" => false);
        $message = format_text($config->message, FORMAT_MOODLE, $options);
        \core\notification::add($message, \core\output\notification::NOTIFY_INFO);
        echo \html_writer::script("(function() {" .
                                  "var notificationHolder = document.getElementById('user-notifications');" .
                                  "if (!notificationHolder) { return; }" .
                                  "notificationHolder.className += ' courseannouncement';" .
                                  "$('#user-notifications').insertAfter('.full-bg');" .
                                  "})();"
        );
    }

    return true;
}

function local_global_banner_course_notification () {
    global $COURSE, $SITE;

    $site = isset($SITE) ? $SITE : get_site();
    $config = get_config("local_global_banner");
    if ($config->visible) {
        //use context_course rather then context_system because of caching
        $options = array("context" => context_course::instance($site->id), "trusted" => true, "para" => false);
        $message = format_text($config->message, FORMAT_MOODLE, $options);
        \core\notification::add($message, \core\output\notification::NOTIFY_INFO);
        echo \html_writer::script("(function() {" .
                                  "var notificationHolder = document.getElementById('user-notifications');" .
                                  "if (!notificationHolder) { return; }" .
                                  "notificationHolder.className += ' courseannouncement'" .
                                  "})();"
        );
    }
    return true;
}
