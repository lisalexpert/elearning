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
 * @package    theme_bootstrap
 * @copyright  2012
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_bootstrap_core_renderer extends core_renderer {
    static $social_buttons;
    public function __construct(moodle_page $page, $target) {
        $current_path = $page->url->get_path();
        $course_index = new moodle_url('/course/index.php');
        $course_path = $course_index->get_path();
        
        if ( $current_path == $course_path && empty($_GET) && empty($_POST))
        {
            $home_index = new moodle_url('/');
            redirect($home_index);
        }
        
        parent::__construct($page, $target);
        
        global $CFG;
        require_once($CFG->dirroot . '/auth/googleoauth2/lib.php');
        $authsequence = get_enabled_auth_plugins(true); // auths, in sequence
        if(is_null(static::$social_buttons) && function_exists('auth_googleoauth2_render_buttons') && (!isloggedin() || isguestuser())){
          static::$social_buttons = auth_googleoauth2_render_buttons();
        }
    }
    public function fatal_error($message, $moreinfourl, $link, $backtrace, $debuginfo = NULL, $errorcode = '') {
        global $CFG,$DB;

        $output = '';
        $obbuffer = '';

        if ($this->has_started()) {
            // we can not always recover properly here, we have problems with output buffering,
            // html tables, etc.
            $output .= $this->opencontainers->pop_all_but_last();

        } else {
            // It is really bad if library code throws exception when output buffering is on,
            // because the buffered text would be printed before our start of page.
            // NOTE: this hack might be behave unexpectedly in case output buffering is enabled in PHP.ini
            error_reporting(0); // disable notices from gzip compression, etc.
            while (ob_get_level() > 0) {
                $buff = ob_get_clean();
                if ($buff === false) {
                    break;
                }
                $obbuffer .= $buff;
            }
            error_reporting($CFG->debug);

            // Output not yet started.
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            if (empty($_SERVER['HTTP_RANGE'])) {
                @header($protocol . ' 404 Not Found');
            } else {
                // Must stop byteserving attempts somehow,
                // this is weird but Chrome PDF viewer can be stopped only with 407!
                @header($protocol . ' 407 Proxy Authentication Required');
            }

            $this->page->set_context(null); // ugly hack - make sure page context is set to something, we do not want bogus warnings here
            $this->page->set_url('/'); // no url
            //$this->page->set_pagelayout('base'); //TODO: MDL-20676 blocks on error pages are weird, unfortunately it somehow detect the pagelayout from URL :-(
            $this->page->set_title(get_string('error'));
            $this->page->set_heading($this->page->course->fullname);
            $output .= $this->header();
        }
        
        $message = '<p class="errormessage">' . $message . '</p>'.
                '<p class="errorcode"><a href="' . $moreinfourl . '">' .
                get_string('moreinformation') . '</a></p>';
        if (empty($CFG->rolesactive)) {
            $message .= '<p class="errormessage">' . get_string('installproblem', 'error') . '</p>';
            //It is usually not possible to recover from errors triggered during installation, you may need to create a new database or use a different database prefix for new installation.
        }
        if ($this->page->course->id != SITEID )
        {
            $coursecontext = context_course::instance($this->page->course->id);
            
            if (!is_enrolled($coursecontext))
            {
                $plugin = enrol_get_plugin('self');
                $instance = $DB->get_record('enrol', array('courseid'=>$this->page->course->id, 'enrol'=>'self'), '*', IGNORE_MISSING);
                if ($instance && !$instance->password){
                    require_once(dirname(__DIR__). '/override/self_enrol_form.php' );
                    $enrolstatus = $plugin->can_self_enrol($instance);
                    // Don't show enrolment instance form, if user can't enrol using it.
                    if (true === $enrolstatus) {
                        $form = new enrol_self_enrol_quick_form(NULL, $instance);
                        ob_start();
                        $form->display();
                        $form_content = html_writer::tag('div', ob_get_clean(),array('class'=>'course_self_enrol_quickform'));
                        $message .= $form_content;
                    }
                }
            }
            
        }
        $output .= $this->box($message, 'errorbox', null, array('data-rel' => 'fatalerror'));

        if ($CFG->debugdeveloper) {
            if (!empty($debuginfo)) {
                $debuginfo = s($debuginfo); // removes all nasty JS
                $debuginfo = str_replace("\n", '<br />', $debuginfo); // keep newlines
                $output .= $this->notification('<strong>Debug info:</strong> '.$debuginfo, 'notifytiny');
            }
            if (!empty($backtrace)) {
                $output .= $this->notification('<strong>Stack trace:</strong> '.format_backtrace($backtrace), 'notifytiny');
            }
            if ($obbuffer !== '' ) {
                $output .= $this->notification('<strong>Output buffer:</strong> '.s($obbuffer), 'notifytiny');
            }
        }

        if (empty($CFG->rolesactive)) {
            // continue does not make much sense if moodle is not installed yet because error is most probably not recoverable
        } else if (!empty($link)) {
            $output .= $this->continue_button($link);
        }

        $output .= $this->footer();

        // Padding to encourage IE to display our error page, rather than its own.
        $output .= str_repeat(' ', 512);

        return $output;
    }
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);

        if ($classes == 'notifyproblem') {
            return html_writer::div($message, 'alert alert-danger');
        }
        if ($classes == 'notifywarning') {
            return html_writer::div($message, 'alert alert-warning');
        }
        if ($classes == 'notifysuccess') {
            return html_writer::div($message, 'alert alert-success');
        }
        if ($classes == 'notifymessage') {
            return html_writer::div($message, 'alert alert-info');
        }
        if ($classes == 'redirectmessage') {
            return html_writer::div($message, 'alert alert-block alert-info');
        }
        if ($classes == 'notifytiny') {
            // Not an appropriate semantic alert class!
            return $this->debug_listing($message);
        }
        return html_writer::div($message, $classes);
    }

    private function debug_listing($message) {
        $message = str_replace('<ul style', '<ul class="list-unstyled" style', $message);
        return html_writer::tag('pre', $message, array('class' => 'alert alert-info'));
    }

    public function navbar() {
        $items = $this->page->navbar->get_items();
        if (empty($items)) { // MDL-46107
            return '';
        }
        $breadcrumbs = '';
        foreach ($items as $item) {
            $item->hideicon = true;
            $breadcrumbs .= '<li>'.$this->render($item).'</li>';
        }
        return "<ol class=breadcrumb>$breadcrumbs</ol>";
    }

    public function custom_menu($custommenuitems = '') {
        // The custom menu is always shown, even if no menu items
        // are configured in the global theme settings page.
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) { // MDL-45507
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }
	protected function render_custom_menu(custom_menu $menu) {
    	/*
    	* This code replaces adds the current enrolled
    	* courses to the custommenu.
    	*/
		
    	$hasdisplaymycourses = true;//(empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
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
            $branchurl   = new moodle_url('/my/index.php');
            $branchsort  = 10000;
 
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
 			if ($courses = enrol_get_my_courses(NULL, 'fullname ASC')) {
				
 				foreach ($courses as $course) {
 					if ($course->visible){
 						$branch->add(format_string($course->fullname), new moodle_url('/course/view.php?id='.$course->id), format_string($course->shortname));
 					}
 				}
 			} else {
                $noenrolments = get_string('noenrolments', 'theme_bootstrap');
 				$branch->add('<em>'.$noenrolments.'</em>', new moodle_url('/'), $noenrolments);
 			}
            
        }
        
        /*
    	* This code replaces adds the My Dashboard
    	* functionality to the custommenu.
    	*/
        $hasdisplaymydashboard = true;//(empty($this->page->theme->settings->displaymydashboard)) ? false : $this->page->theme->settings->displaymydashboard;
        if (isloggedin() && !isguestuser() && $hasdisplaymydashboard) {
            $branchlabel = get_string('mydashboard', 'theme_bootstrap');
            $branchurl   = new moodle_url('/my/index.php');
            $branchtitle = get_string('mydashboard', 'theme_bootstrap');
            $branchsort  = 10000;
 
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
 			$branch->add('<i class="fa fa-user"></i>'.get_string('profile').'',new moodle_url('/user/profile.php'),get_string('profile'));
 			$branch->add('<i class="fa fa-calendar"></i>'.get_string('pluginname', 'block_calendar_month').'',new moodle_url('/calendar/view.php'),get_string('pluginname', 'block_calendar_month'));
 			$branch->add('<i class="fa fa-envelope"></i>'.get_string('messages', 'message').'',new moodle_url('/message/index.php'),get_string('messages', 'message'));
 			// $branch->add('<i class="fa fa-certificate"></i>'.get_string('badges').'',new moodle_url('/badges/mybadges.php'),get_string('badges'));
 			$branch->add('<i class="fa fa-file"></i>'.get_string('privatefiles', 'block_private_files').'',new moodle_url('/user/files.php'),get_string('privatefiles', 'block_private_files'));
 			// $branch->add('<i class="fa fa-sign-out"></i>'.get_string('logout').'',new moodle_url('/login/logout.php'),get_string('logout'));    
      
        }
            $branchlabel = get_string('contact_us', 'theme_bootstrap');
            $branchurl   = new moodle_url('https://www.informea.org/en/contact');
            $branchtitle = get_string('contact_us', 'theme_bootstrap');
            $branchsort  = 12000;
 
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
                $branchurl   = new moodle_url('/my/index.php');
                $branchsort  = 11000;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
                
                $defaultthemecolorslabel = get_string('defaultcolors', 'theme_bootstrap');
                $branch->add('<i class="fa fa-square colours-default"></i>' . $defaultthemecolorslabel,
                        new moodle_url($this->page->url, array('essentialcolours' => 'default')), $defaultthemecolorslabel);
                foreach ($alternativethemes as $alternativethemenumber) {
                    if (!empty($this->page->theme->settings->{'alternativethemename' . $alternativethemenumber})) {
                        $alternativethemeslabel = $this->page->theme->settings->{'alternativethemename' . $alternativethemenumber};
                    } else {
                        $alternativethemeslabel = get_string('alternativecolors', 'theme_bootstrap', $alternativethemenumber);
                    }
                    $branch->add('<i class="fa fa-square colours-alternative' .  $alternativethemenumber . '"></i>' . $alternativethemeslabel,
                            new moodle_url($this->page->url, array('essentialcolours' => 'alternative' . $alternativethemenumber)), $alternativethemeslabel);
                }
            }
        }

        // TODO: eliminate this duplicated logic, it belongs in core, not
        // here. See MDL-39565.

        $content = '<ul class="nav navbar-nav" id="navbar-menu">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.'</ul>';
    }

    public function user_menu($user = NULL, $withlinks = NULL) {
        global $CFG;
        $usermenu = new custom_menu('', current_language());
        return $this->render_user_menu($usermenu);
    }

    protected function render_user_menu(custom_menu $menu) {
        global $CFG, $USER, $DB;

        $addusermenu = true;
        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
        foreach($langs as $key=>$lang){
            $first_word = strtok($lang, " ");
            $langs[$key] = $first_word;
        }
        if (count($langs) < 2
        or empty($CFG->langmenu)
        or ($this->page->course != SITEID and !empty($this->page->course->lang))) {
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
                $usermenu = $menu->add('<small>My account</small><i class="fa fa-user hasauth" id="cust-user-ref-id"></i>', new moodle_url('#'), fullname($USER), 10001);
                
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
                
                if (!function_exists("user_get_user_navigation_info"))
                {
                    require_once($CFG->dirroot . '/user/lib.php');
                }
                
                $opts = user_get_user_navigation_info($USER, $this->page);
                foreach ($opts->navitems as $key => $value)
                {
                    if ($value->itemtype == 'link')
                    {
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) 
                        {
                            switch ($value->pix)
                            {
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
                        else if (isset($value->imgsrc) && !empty($value->imgsrc)) 
                        {
                            $pix = '<span class="glyphicon glyphicon-cog"></span>';
                        }
                        
                        
                        $usermenu->add(
                            $pix . $value->title,
                            $value->url,
                            $value->title
                        );
                    }
                    
                }
            } else {
                $usermenu = $menu->add('<small>Login</small><i class="fa fa-user noauth" id="cust-user-ref-id"></i>', new moodle_url('/login/index.php'), get_string('login'), 10001);
                $usermenu->add('</a>' . $this->login_form() . '<a>', new moodle_url('#'), '');
            }
        }
		$content = '<ul id="navbar-menu2" class="nav navbar-nav navbar-right">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.'</ul>';
    }
	public function course_search_form($value = '', $format = 'plain') {
        static $count = 0;
        $formid = 'navbar-search';
        if ((++$count) > 1) {
            $formid .= $count;
        }
        $inputid = 'coursesearchbox';
        $inputsize = 30;

        // if ($format === 'navbar') {
            // $formid = 'coursesearchnavbar';
            // $inputid = 'navsearchbox';
        // }

        $strsearchcourses = get_string("searchcourses");
        $searchurl = new moodle_url('/course/search.php');

        $form = array('id' => $formid, 'action' => $searchurl, 'method' => 'get', 'class' => "form-inline", 'role' => 'form');
        $output = html_writer::start_tag('form', $form);
        $output .= html_writer::start_div('input-group');
        $output .= html_writer::tag('label', $strsearchcourses, array('for' => $inputid, 'class' => 'sr-only'));
        $search = array('type' => 'text', 'id' => $inputid, 'size' => $inputsize, 'name' => 'search',
                        'class' => 'form-control', 'value' => s($value), 'placeholder' => $strsearchcourses);
        $output .= html_writer::empty_tag('input', $search);
        $button = array('type' => 'submit', 'class' => 'btn btn-warning');
        $output .= html_writer::start_span('input-group-btn');
        $output .= html_writer::tag('button', '<i class="fa fa-search"></i>', $button);
        $output .= html_writer::end_span();
        $output .= html_writer::end_div(); // Close form-group.
        $output .= html_writer::end_tag('form');

        return $output;
    }
    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0 ) {
        static $submenucount = 0;

        if ($menunode->has_children()) {

            if ($level == 1) {
                $dropdowntype = 'dropdown';
            } else {
                $dropdowntype = 'dropdown-submenu';
            }

            $content = html_writer::start_tag('li', array('class' => $dropdowntype));
            // If the child has menus render it as a sub menu.
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $linkattributes = array(
                'href' => (strpos($url,'javascript')===0)?str_replace('//','',$url):$url,
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'title' => $menunode->get_title(),
            );
            $content .= html_writer::start_tag('a', $linkattributes);
            $content .= $menunode->get_text();
            if ($level == 1) {
                $content .= '<b class="caret"></b>';
            }
            $content .= '</a>';
            $content .= '<ul class="dropdown-menu">';
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= '</ul>';
        } else {
            $content = '<li>';
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title()));
        }
        return $content;
    }

    protected function render_tabtree(tabtree $tabtree) {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $firstrow = $secondrow = '';
        foreach ($tabtree->subtree as $tab) {
            $firstrow .= $this->render($tab);
            if (($tab->selected || $tab->activated) && !empty($tab->subtree) && $tab->subtree !== array()) {
                $secondrow = $this->tabtree($tab->subtree);
            }
        }
        return html_writer::tag('ul', $firstrow, array('class' => 'nav nav-tabs nav-justified')) . $secondrow;
    }

    protected function render_tabobject(tabobject $tab) {
        if ($tab->selected or $tab->activated) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'active'));
        } else if ($tab->inactive) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'disabled'));
        } else {
            if (!($tab->link instanceof moodle_url)) {
                // Backward compatibility when link was passed as quoted string.
                $link = "<a href=\"$tab->link\" title=\"$tab->title\">$tab->text</a>";
            } else {
                $link = html_writer::link($tab->link, $tab->text, array('title' => $tab->title));
            }
            return html_writer::tag('li', $link);
        }
    }

    public function box($contents, $classes = 'generalbox', $id = null, $attributes = array()) {
        if (isset($attributes['data-rel']) && $attributes['data-rel'] === 'fatalerror') {
            return html_writer::div($contents, 'alert alert-danger', $attributes);
        }
        return parent::box($contents, $classes, $id, $attributes);
    }

    public function content_zoom() {
		$content = "";
		
		/* if(isloggedin()){
			$zoomin = html_writer::span(get_string('fullscreen', 'theme_bootstrap'), 'zoomin');
			$zoomout = html_writer::span(get_string('closefullscreen', 'theme_bootstrap'), 'zoomout');
			$content = html_writer::link('#',  $zoomin . $zoomout,
				array('class' => 'btn btn-default pull-right moodlezoom'));
		} */
        return $content;
    }
	public function block(block_contents $bc, $region) {
		$docked = get_user_preferences('docked_block_instance_'.$bc->blockinstanceid, -1);
		if($docked){
			$bc->add_class('dock_on_load');
		}
		return parent::block($bc,$region);
	}
	public function blocks($region, $classes = array(), $tag = 'aside') {
        $displayregion = $this->page->apply_theme_region_manipulations($region);
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = array(
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $displayregion),
            'class' => join(' ', $classes),
            'data-blockregion' => $displayregion,
            'data-droptarget' => '1'
        );
        if ($this->page->blocks->region_has_content($displayregion, $this)) {
            $content = $this->blocks_for_region($displayregion);
        } else {
            $content = '';
        }
        return html_writer::tag($tag, $content, $attributes);
    }
	public function page_heading($tag = 'h1') {
        return parent::page_heading($tag). html_writer::tag('div', format_text($this->page->course->summary,FORMAT_HTML,array('para'=>false)), array('id' => 'fpCourseSummary'));
    }
	public function home_link() {
        global $CFG, $SITE;
		if (!empty($CFG->target_release) && $CFG->target_release != $CFG->release) {
            // Special case for during install/upgrade.
            return '<div class="sitelink">'.
                   '<a title="Moodle" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">' .
                   '<img src="' . $this->pix_url('moodlelogo') . '" alt="moodlelogo" /></a></div>';

        } else if ($this->page->course->id == $SITE->id || strpos($this->page->pagetype, 'course-view') === 0) {
            return '';

        } else {
            return '<div class="homelink"><a href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->page->course->id . '">' .
                    format_string($this->page->course->shortname, true, array('context' => $this->page->context)) . '</a></div>';
        }
    }
	public function login_info($withlinks = null) {
        global $USER, $CFG, $DB, $SESSION;

        if (during_initial_install()) {
            return '';
        }

        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        $loginpage = ((string)$this->page->url === get_login_url());
        $course = $this->page->course;
        if (\core\session\manager::is_loggedinas()) {
            $realuser = \core\session\manager::get_realuser();
            $fullname = fullname($realuser, true);
            if ($withlinks) {
                $loginastitle = get_string('loginas');
                $realuserinfo = " [<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;sesskey=".sesskey()."\"";
                $realuserinfo .= "title =\"".$loginastitle."\">$fullname</a>] ";
            } else {
                $realuserinfo = " [$fullname] ";
            }
        } else {
            $realuserinfo = '';
        }

        $loginurl = get_login_url();

        if (empty($course->id)) {
            // $course->id is not defined during installation
            return '';
        } else if (isloggedin()) {
            $context = context_course::instance($course->id);

            $fullname = fullname($USER, true);
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            if ($withlinks) {
                $linktitle = get_string('viewprofile');
                $username = "<a href=\"$CFG->wwwroot/user/profile.php?id=$USER->id\" title=\"$linktitle\">$fullname</a>";
            } else {
                $username = $fullname;
            }
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                if ($withlinks) {
                    $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
                } else {
                    $username .= " from {$idprovider->name}";
                }
            }
            if (isguestuser()) {
                $loggedinas = "";
                // $loggedinas = $realuserinfo.get_string('loggedinasguest');
                // if (!$loginpage && $withlinks) {
                    // $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
                // }
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.role_get_name($role, $context);
                }
                $loggedinas = get_string('loggedinas', 'moodle', $username).$rolename;
                if ($withlinks) {
                    $url = new moodle_url('/course/switchrole.php', array('id'=>$course->id,'sesskey'=>sesskey(), 'switchrole'=>0, 'returnurl'=>$this->page->url->out_as_local_url(false)));
                    $loggedinas .= ' ('.html_writer::tag('a', get_string('switchrolereturn'), array('href' => $url)).')';
                }
            } else {
				$loggedinas = "";
                // $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username);
                // if ($withlinks) {
                    // $loggedinas .= " <a class='btn btn-special' href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\">".get_string('logout').'</a>';
                // }
            }
        } elseif(strpos($this->page->url,'signup.php')===false){
			$signupurl = "$CFG->wwwroot/login/signup.php";

			if (!empty($CFG->loginhttps)) {
				$signupurl = str_replace('http:', 'https:', $signupurl);
			}

            $loggedinas = '';
            if (!$loginpage && $withlinks) {
                // $loggedinas .= "<a class='btn btn-special' href=\"$signupurl\">".get_string('startsignup').'</a>';
            }
        }

        $loggedinas = '<div class="logininfo">'.$loggedinas.'</div>';

        if (isset($SESSION->justloggedin)) {
            unset($SESSION->justloggedin);
            if (!empty($CFG->displayloginfailures)) {
                if (!isguestuser()) {
                    // Include this file only when required.
                    require_once($CFG->dirroot . '/user/lib.php');
                    if ($count = user_count_login_failures($USER)) {
                        $loggedinas .= '<div class="loginfailures">';
                        $a = new stdClass();
                        $a->attempts = $count;
                        $loggedinas .= get_string('failedloginattempts', '', $a);
                        if (file_exists("$CFG->dirroot/report/log/index.php") and has_capability('report/log:view', context_system::instance())) {
                            $loggedinas .= ' ('.html_writer::link(new moodle_url('/report/log/index.php', array('chooselog' => 1,
                                    'id' => 0 , 'modid' => 'site_errors')), get_string('logs')).')';
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }
    
    function login_form(){
      ob_start(); ?>
<div id="quick-login-menu">
  
  <?php $form_action = new moodle_url('/login/index.php'); ?>
  <form id="quick-signin-form" action="<?php echo $form_action; ?>" method="post" accept-charset="utf-8" autocomplete="off" />
    <h2>Login in your private area</h2>
    <label id="quick-for-username" for="quick-username"><?php echo get_string('usernameemail'); ?></label>
    <input maxlength="100" size="30" name="username" type="text" id="quick-username" required="required" />
    <label id="quick-for-password" for="quick-password"><?php echo get_string('password'); ?></label>
    <input type="password" name="password" id="quick-password" value="" data-size="12" size="12" required="required" />
    <button type="submit">Send</button>
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
    <div class="quick_social_icons">
      <?php echo static::$social_buttons; ?>
    </div>
    <?php $signup_action = new moodle_url('/login/signup.php'); ?>
    <a id="quick-signin-signup" href="<?php echo $signup_action; ?>">Create your account</a>
    <small><a href="<?php echo new moodle_url('/login/forgot_password.php') ?>"><?php echo get_string('forgotten'); ?></a></small>
  </form>
</div>
      <?php
      return ob_get_clean();
    }
}