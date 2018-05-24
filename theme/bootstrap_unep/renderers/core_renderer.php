<?php
// This file is part of The Bootstrap 3 Moodle theme
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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrap_unep
 * @copyright  2018
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_bootstrap_unep_core_renderer extends theme_bootstrap_core_renderer {
  protected function render_user_menu(custom_menu $menu) {
    global $CFG, $USER, $DB;

    $addusermenu = true;
    $addlangmenu = true;
    $langs = get_string_manager()->get_list_of_translations();
    foreach($langs as $key=>$lang){
      $first_word = strtok($lang, " ");
      $langs[$key] = $first_word;
    }
    if (count($langs) < 2 || empty($CFG->langmenu) || ($this->page->course != SITEID and !empty($this->page->course->lang))) {
      $addlangmenu = false;
    }
    if ($addlangmenu) {
      $language = $menu->add($langs[current_language()], new moodle_url('#'), get_string('language'), 10002);
      foreach ($langs as $langtype => $langname) {
        $language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
      }
    }

    if ($addusermenu) {
      if (isloggedin() && !isguestuser()) {
        $usermenu = $menu->add('<span>My account</span>', new moodle_url('#'), fullname($USER), 10001);
        
        /*
        $usermenu->add(
          '<span class="glyphicon glyphicon-off"></span>' . get_string('logout'),
          new moodle_url('/login/logout.php', array('sesskey' => sesskey(), 'alt' => 'logout')),
          get_string('logout')
        );

        $usermenu->add(
          '<span class="glyphicon glyphicon-user"></span>' . get_string('viewprofile'),
          new moodle_url('/user/profile.php', array('id' => $USER->id)),
          get_string('viewprofile')
        );

        $usermenu->add(
          '<span class="glyphicon glyphicon-cog"></span>' . get_string('editmyprofile'),
          new moodle_url('/user/edit.php', array('id' => $USER->id)),
          get_string('editmyprofile')
        );
        */
        
        if (!function_exists("user_get_user_navigation_info")) {
          require_once($CFG->dirroot . '/user/lib.php');
        }
        
        $opts = user_get_user_navigation_info($USER, $this->page);
        foreach ($opts->navitems as $key => $value) {
          if ($value->itemtype == 'link') {
            $pix = null;
            if (isset($value->pix) && !empty($value->pix)) {
              switch ($value->pix) {
                case "i/course":
                  $pix = '<span class="glyphicon glyphicon-home"></span>';
                  break;
                case "i/user":
                  $pix = '<span class="glyphicon glyphicon-user"></span>';
                  break;
                case "t/grades":
                  $pix = '<span class="glyphicon glyphicon-tasks"></span>';
                  break;
                case "t/message":
                  $pix = '<span class="glyphicon glyphicon-envelope"></span>';
                  break;
                case "t/preferences":
                  $pix = '<span class="glyphicon glyphicon-cog"></span>';
                  break;
                case "a/logout":
                  $pix = '<span class="glyphicon glyphicon-log-out"></span>';
                  break;
                case "i/switchrole":
                  $pix = '<span class="glyphicon glyphicon-retweet"></span>';
                  break;
              }
            } 
            else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
              $pix = '<span class="glyphicon glyphicon-cog"></span>';
            }
            
            
            $usermenu->add(
              $pix . $value->title,
              $value->url,
              $value->title
            );
          }
          
        }
      }
      else {
        $usermenu = $menu->add('<span>Login</span>', new moodle_url('/login/index.php'), get_string('login'), 10001);
        $usermenu->add('</a>' . $this->login_form() . '<a>', new moodle_url('#'), '');
      }
    }
    $content = '<ul id="navbar-menu2" class="nav navbar-nav navbar-right">';
    foreach ($menu->get_children() as $item) {
      $content .= $this->render_custom_menu_item($item, 1);
    }

    return $content.'</ul>';
  }
  
  protected function render_custom_menu(custom_menu $menu) {
    /*
     * This code replaces adds the current enrolled
     * courses to the custommenu.
     */

    $hasdisplaymycourses = true; //(empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
    if (isloggedin() && !isguestuser() && $hasdisplaymycourses) {
      $mycoursetitle = $this->page->theme->settings->mycoursetitle;
      if ($mycoursetitle == 'module') {
        $branchtitle = get_string('mymodules', 'theme_bootstrap');
      } else if ($mycoursetitle == 'unit') {
        $branchtitle = get_string('myunits', 'theme_bootstrap');
      } else if ($mycoursetitle == 'class') {
        $branchtitle = get_string('myclasses', 'theme_bootstrap');
      } else {
        $branchtitle = get_string('mycourses', 'theme_bootstrap');
      }
      $branchlabel = $branchtitle;
      $branchurl = new moodle_url('/my/index.php');
      $branchsort = 10000;

      $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
      if ($courses = enrol_get_my_courses(NULL, 'fullname ASC')) {
        foreach ($courses as $course) {
          if ($course->visible) {
            $branch->add(format_string($course->fullname), new moodle_url('/course/view.php?id=' . $course->id), format_string($course->shortname));
          }
        }
      } else {
        $noenrolments = get_string('noenrolments', 'theme_bootstrap');
        $branch->add('<em>' . $noenrolments . '</em>', new moodle_url('/'), $noenrolments);
      }
    }

    /*
     * This code replaces adds the My Dashboard
     * functionality to the custommenu.
     */
    $hasdisplaymydashboard = true; //(empty($this->page->theme->settings->displaymydashboard)) ? false : $this->page->theme->settings->displaymydashboard;
    if (isloggedin() && !isguestuser() && $hasdisplaymydashboard) {
      $branchlabel = get_string('mydashboard', 'theme_bootstrap');
      $branchurl = new moodle_url('/my/index.php');
      $branchtitle = get_string('mydashboard', 'theme_bootstrap');
      $branchsort = 10000;

      $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
      $branch->add('<i class="fa fa-user"></i>' . get_string('profile') . '', new moodle_url('/user/profile.php'), get_string('profile'));
      $branch->add('<i class="fa fa-calendar"></i>' . get_string('pluginname', 'block_calendar_month') . '', new moodle_url('/calendar/view.php'), get_string('pluginname', 'block_calendar_month'));
      $branch->add('<i class="fa fa-envelope"></i>' . get_string('messages', 'message') . '', new moodle_url('/message/index.php'), get_string('messages', 'message'));
      // $branch->add('<i class="fa fa-certificate"></i>'.get_string('badges').'',new moodle_url('/badges/mybadges.php'),get_string('badges'));
      $branch->add('<i class="fa fa-file"></i>' . get_string('privatefiles', 'block_private_files') . '', new moodle_url('/user/files.php'), get_string('privatefiles', 'block_private_files'));
      // $branch->add('<i class="fa fa-sign-out"></i>'.get_string('logout').'',new moodle_url('/login/logout.php'),get_string('logout'));   
    }
    $branchlabel = get_string('contact_us', 'theme_bootstrap');
    $branchurl = new moodle_url('https://www.informea.org/en/contact');
    $branchtitle = get_string('contact_us', 'theme_bootstrap');
    $branchsort = 12000;

    $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

    /*
     * This code adds the Theme colors selector to the custommenu.
     */
    if (isloggedin() && !isguestuser()) {
      $alternativethemes = array();
      foreach (range(1, 3) as $alternativethemenumber) {
        if (!empty($this->page->theme->settings->{'enablealternativethemecolors' . $alternativethemenumber})) {
          $alternativethemes[] = $alternativethemenumber;
        }
      }
      if (!empty($alternativethemes)) {
        $branchtitle = get_string('themecolors', 'theme_bootstrap');
        $branchlabel = '<i class="fa fa-th-large"></i>' . $branchtitle;
        $branchurl = new moodle_url('/my/index.php');
        $branchsort = 11000;
        $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

        $defaultthemecolorslabel = get_string('defaultcolors', 'theme_bootstrap');
        $branch->add('<i class="fa fa-square colours-default"></i>' . $defaultthemecolorslabel, new moodle_url($this->page->url, array('essentialcolours' => 'default')), $defaultthemecolorslabel);
        foreach ($alternativethemes as $alternativethemenumber) {
          if (!empty($this->page->theme->settings->{'alternativethemename' . $alternativethemenumber})) {
            $alternativethemeslabel = $this->page->theme->settings->{'alternativethemename' . $alternativethemenumber};
          } else {
            $alternativethemeslabel = get_string('alternativecolors', 'theme_bootstrap', $alternativethemenumber);
          }
          $branch->add('<i class="fa fa-square colours-alternative' . $alternativethemenumber . '"></i>' . $alternativethemeslabel, new moodle_url($this->page->url, array('essentialcolours' => 'alternative' . $alternativethemenumber)), $alternativethemeslabel);
        }
      }
    }

    $content = '<ul id="navbar-menu" class="nav navbar-nav navbar-right">';
    foreach ($menu->get_children() as $item) {
      $content .= $this->render_custom_menu_item($item, 1);
    }

    return $content . '</ul>';
  }
}