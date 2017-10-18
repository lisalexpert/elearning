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

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrap
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/renderer.php");

class theme_bootstrap_core_course_renderer extends core_course_renderer {

    protected function addEnrolOptions($course) {
		if ($course->id == SITEID) return false;
		$link = "";
		$icon = "";
		$title = "";
		$attribs = array();
		
		$coursecontext = context_course::instance($course->id);
		$instances = enrol_get_instances($course->id, true);
		$plugins   = enrol_get_plugins(true);
		
		if (isguestuser() or !isloggedin()) {
			// guest account can not be enrolled - no links for them
		} else if (is_enrolled($coursecontext)) {
			// unenrol link if possible
			
			foreach ($instances as $instance) {
				if (!isset($plugins[$instance->enrol])) {
					continue;
				}
				$plugin = $plugins[$instance->enrol];
				if ($unenrollink = $plugin->get_unenrolself_link($instance)) {
					$shortname = format_string($course->shortname, true, array('context' => $coursecontext));
					$link = $unenrollink;
					$title = get_string('unenrolme', 'core_enrol', $shortname);
					$icon = html_writer::tag('span', '', array('class' => ' glyphicon glyphicon-log-out'));
					break;
				}
			}
		} else {
			
			// enrol link if possible
			if (is_viewing($coursecontext)) {
				// better not show any enrol link, this is intended for managers and inspectors
			} else {
				foreach ($instances as $instance) {
					if (!isset($plugins[$instance->enrol])) {
						continue;
					}
					$plugin = $plugins[$instance->enrol];
					if ($plugin->show_enrolme_link($instance)) {
						$link = new moodle_url('/enrol/index.php', array('id'=>$course->id));
						$shortname = format_string($course->shortname, true, array('context' => $coursecontext));
						$title = get_string('enrolme', 'core_enrol', $shortname);
						$icon = $this->render(new pix_icon('withoutkey', $title, 'enrol_self'));
						break;
					}
				}
			}
		}
		if(!$icon) return false;
		$attribs['title'] = $title;
		$attribs['class'] = 'btn btn-xs';
		$enrolIcon = html_writer::link($link, $icon, $attribs);
		return $enrolIcon;
	}
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG,$DB;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
		$collapsed = true;//strpos($additionalclasses,'first')===false;
        $classes = trim('panel panel-default coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= $collapsed?' collapsed':' in';
        }

        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'panel-heading'));

        // course name
        $coursename = $chelper->get_course_formatted_name($course);
        $coursecontext = context_course::instance($course->id);
		$shortname = format_string($course->shortname, true, array('context' => $coursecontext));
		$clean_shortname = clean_param(preg_replace('/[^\da-z+]/i', '_', $shortname), PARAM_ALPHANUMEXT);
		$coursenamelink = html_writer::link('#'.$clean_shortname,
                                            $coursename, array('class' =>'accordion-toggle'.($collapsed?' collapsed':''),'data-toggle'=>'collapse','data-parent'=>'#courseAccordion'));
        $content .= html_writer::tag('span', $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('span', array('class' => 'moreinfo'));
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $image = html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/info'),
                    'alt' => $this->strings->summary));
                $content .= html_writer::link($url, $image, array('title' => $this->strings->summary));
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('span'); // .moreinfo

        // print enrolmenticons
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
			$content .= $this->addEnrolOptions($course);
			
            // foreach ($icons as $pix_icon) {
                // $content .= $this->render($pix_icon);
            // }
            $content .= html_writer::end_tag('div'); // .enrolmenticons
        }

        $content .= html_writer::end_tag('div'); // .info

        $content .= html_writer::start_tag('div', array('id'=>$clean_shortname,'class' => 'panel-collapse'.($collapsed?' collapse':'in')));
        $content .= html_writer::start_tag('div', array('class' => 'content panel-body'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);

		$icondirection = 'left';
		if ('ltr' === get_string('thisdirection', 'langconfig')) {
			$icondirection = 'right';
		}
		$arrow = html_writer::tag('span', '', array('class' => ' glyphicon glyphicon-arrow-'.$icondirection));
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if (is_enrolled($coursecontext)) {
                $btn = html_writer::tag('span', get_string('course') . ' ' . $arrow, array('class' => 'coursequicklink'));
                $coursebtn = html_writer::link(new moodle_url('/course/view.php',
                    array('id' => $course->id)), $btn, array('class' => 'btn btn-info btn-sm pull-right'));
                $content .= html_writer::tag('div', $coursebtn, array('class' => 'coursebtn'));
            }
        }
		
		// $image = html_writer::empty_tag('img', array('src' => $this->output->pix_url('a/view_icon_active'),
                    // 'alt' => $this->strings->summary));
		if(!is_enrolled($coursecontext)){
            $plugin = enrol_get_plugin('self');
            $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'self'), '*', IGNORE_MISSING);
            if ($instance && !$instance->password){
                require_once(dirname(__DIR__). '/override/self_enrol_form.php' );
                $enrolstatus = $plugin->can_self_enrol($instance);
                // Don't show enrolment instance form, if user can't enrol using it.
                if (true === $enrolstatus) {
                    $form = new enrol_self_enrol_quick_form(NULL, $instance);
                    ob_start();
                    $form->display();
                    $form_content = html_writer::tag('div', ob_get_clean(),array('class'=>'course_self_enrol_quickform','style'=>'display:none;'));
                    $text = ' '.get_string("takecourse",'theme_bootstrap');
                    $coursenamelink = html_writer::link(new moodle_url('/enrol/id.php', array('id' => $course->id)),
                                                        $text.' '.$arrow,array('title'=>$coursename,'class' => $course->visible ? 'btn btn-info btn-sm pull-right' : 'btn btn-info btn-sm pull-right dimmed','onclick'=>'jQuery(this).parent().parent().find(".course_self_enrol_quickform > form").submit();return false;'));
                    $content .= html_writer::tag('span', $coursenamelink, array('class' => 'courselink'));
                    $content .= $form_content;
                }
            }
            $text = ' '.get_string("preview");
			$coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
												$text.' '.$arrow,array('title'=>$coursename,'class' => $course->visible ? 'btn btn-info btn-sm pull-right' : 'btn btn-info btn-sm pull-right dimmed'));
			$content .= html_writer::tag('span', $coursenamelink, array('class' => 'courselink'));
		}
        $content .= html_writer::end_tag('div'); // .content
        $content .= html_writer::end_tag('div'); // .content

        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }
	 protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // prepare content of paging bar if it is needed
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // there are more results that can fit on one page
            if ($paginationurl) {
                // the option paginationurl was specified, display pagingbar
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of courses
        $attributes = $chelper->get_and_erase_attributes('courses');
		$attributes['id'] = 'courseAccordion';
		$attributes['class'].= ' accordion';
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 0;
        foreach ($courses as $course) {
            $coursecount ++;
            $classes = ($coursecount%2) ? 'odd' : 'even';
            if ($coursecount == 1) {
                $classes .= ' first';
            }
            if ($coursecount >= count($courses)) {
                $classes .= ' last';
            }
            $content .= $this->coursecat_coursebox($chelper, $course, $classes);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // .courses
        $content .= '
<script>
// Opening accordion based on URL
var url = document.location.toString();
if ( url.match("#") ) {
	$el = $("#"+url.split("#")[1]);
	if($el.length){
		$el.addClass("in");
		$("html, body").animate({
			scrollTop: $el.prev(".panel-heading").offset().top
		}, 500);
	}
}
</script>
		'; // .courses
        return $content;
    }
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $CFG;
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';

        // Display course overview files.
        $contentimages = $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                    $contentimages .= html_writer::start_tag('div', array('class' => 'imagebox'));

                    $images = html_writer::empty_tag('img', array('src' => $url, 'alt' => 'Course Image '. $course->fullname,
                        'class' => 'courseimage'));
                    $contentimages .= html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), $images);

                    $contentimages .= html_writer::end_tag('div');
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                        html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        $content .= $contentimages. $contentfiles;

        // Display course summary.
        if ($course->has_summary()) {
            $content .= $chelper->get_course_formatted_summary($course);
        }


        // Display course contacts. See course_in_list::get_course_contacts().
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $name = $coursecontact['rolename'].': '.
                        html_writer::link(new moodle_url('/user/view.php',
                                array('id' => $userid, 'course' => SITEID)),
                            $coursecontact['username']);
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul'); // .teachers
        }

        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // .coursecat
            }
        }

        return $content;
    }

    public function course_search_form($value = '', $format = 'plain') {
        return;
        static $count = 0;
        $formid = 'coursesearch';
        if ((++$count) > 1) {
            $formid .= $count;
        }
        $inputid = 'coursesearchbox';
        $inputsize = 30;

        if ($format === 'navbar') {
			return "";
            $formid = 'coursesearchnavbar';
            $inputid = 'navsearchbox';
        }

        $strsearchcourses = get_string("searchcourses");
        $searchurl = new moodle_url('/course/search.php');

        $form = array('id' => $formid, 'action' => $searchurl, 'method' => 'get', 'class' => "form-inline", 'role' => 'form');
        $output = html_writer::start_tag('form', $form);
        $output .= html_writer::start_div('input-group');
        $output .= html_writer::tag('label', $strsearchcourses, array('for' => $inputid, 'class' => 'sr-only'));
        $search = array('type' => 'text', 'id' => $inputid, 'size' => $inputsize, 'name' => 'search',
                        'class' => 'form-control', 'value' => s($value), 'placeholder' => $strsearchcourses);
        $output .= html_writer::empty_tag('input', $search);
        $button = array('type' => 'submit', 'class' => 'btn btn-default');
        $output .= html_writer::start_span('input-group-btn');
        $output .= html_writer::tag('button', get_string('go'), $button);
        $output .= html_writer::end_span();
        $output .= html_writer::end_div(); // Close form-group.
        $output .= html_writer::end_tag('form');

        return $output;
    }
    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
        global $CFG;
        $subcategories = array();
        if (!$chelper->get_categories_display_option('nodisplay')) {
            $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
        }
        $totalcount = $coursecat->get_children_count();
        if (!$totalcount) {
            // Note that we call coursecat::get_children_count() AFTER coursecat::get_children() to avoid extra DB requests.
            // Categories count is cached during children categories retrieval.
            return '';
        }

        // prepare content of paging bar or more link if it is needed
        $paginationurl = $chelper->get_categories_display_option('paginationurl');
        $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
        if ($totalcount > count($subcategories)) {
            if ($paginationurl) {
                // the option 'paginationurl was specified, display pagingbar
                $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_categories_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $viewmoreurl->param('categoryid', $coursecat->id);
                }
                $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of subcategories
        $content = html_writer::start_tag('div', array('class' => 'subcategories'));

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        foreach ($subcategories as $subcategory) {
            $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div');
        return $content;
    }
    public function frontpage_combo_list() {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');
        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT);
        $chelper->set_subcat_depth($CFG->maxcategorydepth)->
            set_categories_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'categories', 'page' => 1))
            ))->
            set_courses_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'courses', 'page' => 1))
            ))->
            set_attributes(array('class' => 'frontpage-category-combo'));
        return $this->coursecat_tree($chelper, coursecat::get(0));
    }
    protected function coursecat_tree(coursecat_helper $chelper, $coursecat) {
        $categorycontent = $this->coursecat_category_content($chelper, $coursecat, 0);
        if (empty($categorycontent)) {
            return '';
        }

        // Start content generation
        $content = '';
        $attributes = $chelper->get_and_erase_attributes('course_category_tree clearfix');
        $content .= html_writer::start_tag('div', $attributes);

        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .course_category_tree

        return $content;
    }
    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {

      if(($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED) && ($GLOBALS['categoryid']  == $coursecat->id)){
        return "";
      }
      
      // open category tag
      $classes = array('category');
      if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT){
        $classes[] = 'col-md-2 col-sm-6 col-xs-6';
      } else if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT){
        $classes[] = 'col-md-12 col-sm-12 col-xs-12';
      }
      $classes[] = 'categ-' . $coursecat->idnumber;
      $classes[] = 'category-' . $coursecat->id;
      if (empty($coursecat->visible)) {
          $classes[] = 'dimmed_category';
      }
      $categorycontent = strip_tags($chelper->get_category_formatted_description($coursecat),'<img>');
      
      if($categorycontent) {
        $doc = new DOMDocument();
        $doc->loadHTML($categorycontent);    
        $selector = new DOMXPath($doc);

        $result = $selector->query('//img');
        
        // loop through all found items
        foreach($result as $node) {
          $src = $node->getAttribute('src');
          $ext = pathinfo($src, PATHINFO_EXTENSION);
          if($ext == 'svg'){
            $svgcontent = file_get_contents($src);
            $dom = new DOMDocument();
            $dom->loadXML($svgcontent);
            $svg = $dom->getElementsByTagName('svg');
            if($svg){
              $categorycontent = '<div class="svg-content"><div class="svg-content1"><div class="svg-content2">' . $svg->item(0)->C14N() . '</div></div></div>';
            }
          }
          break;
        }
      }
      

      // Make sure JS file to expand category content is included.
      $this->coursecat_include_js();

      $content = html_writer::start_tag('div', array(
          'class' => join(' ', $classes),
          'data-categoryid' => $coursecat->id,
          'data-depth' => $depth,
          'data-showcourses' => $chelper->get_show_courses(),
          'data-type' => self::COURSECAT_TYPE_CATEGORY,
      ));

      // category name
      $categoryname = $coursecat->get_formatted_name();
      $show_courses = $chelper->get_show_courses();
      if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED){
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COUNT);
      }
      if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT
              && ($coursescount = $coursecat->get_courses_count())) {
          $categoryname = '<span class="category-name-wrapper"><span class="category-name">'. $categoryname . '</span></span>'  . html_writer::tag('span', get_string('ncourses','theme_bootstrap', $coursescount ),
                  array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
      } else {
        // $categoryname = '<span class="category-name-wrapper"><span class="category-name">' . $categoryname .'</span></span>' . html_writer::tag('span', get_string('ncourses','theme_bootstrap', 0 ),array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse nocourse'));
      }
      $myBox = html_writer::start_tag('div', array('class' => 'box'));
      $myBox .= html_writer::tag('div', $categorycontent, array('class' => 'content'));
      $myBox .= html_writer::start_tag('div', array('class' => 'info'));
  
      $myBox .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, array('class' => 'categoryname'));
      $myBox .= html_writer::end_tag('div'); 
      $myBox .= html_writer::end_tag('div'); // .info

      // add category content to the output

      $content .= html_writer::link(new moodle_url('/course/index.php',
				array('categoryid' => $coursecat->id)),
				$myBox);
      
      if($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT){
      
        $coursescontent = "";
        $courses = $coursecat->get_courses($chelper->get_courses_display_options());
        $coursescontent = html_writer::start_tag('ul',array('class'=>'categ-courses'));
        foreach ($courses as $course) {
          $coursescontent .= html_writer::start_tag('li');
          
          $coursename = $chelper->get_course_formatted_name($course);
          $courselink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),$coursename,array('class'=>'categ-course-link'));
          
          $coursescontent .= html_writer::start_tag('span',array('class'=>'categ-link'));
          $coursescontent .= $courselink;
          $coursescontent .= html_writer::end_tag('span');
          
          
          $dom = new DOMDocument();
          $coursecontext = context_course::instance($course->id);
          $summary = file_rewrite_pluginfile_urls(format_text($course->summary,FORMAT_HTML,array('para'=>false)), 'pluginfile.php', $coursecontext->id, 'course', 'summary', null);
          $dom->loadHTML('<?xml encoding="utf-8" ?>' . $summary);
          $classname = 'course_summary_flyer';
          $a = new DOMXPath($dom);
          $spans = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
          $downloadlink = '';
          
          for ($i = $spans->length - 1; $i > -1; $i--) {
            $span = $spans->item($i);
            $flyer_url = $spans->item($i)->getAttribute('href'); 
            $downloadlink = html_writer::link(new moodle_url($flyer_url),get_string("downloadflyer",'theme_bootstrap'),array('target'=>'_BLANK','class'=>'categ-button button-download-flyer'));
            $span->parentNode->removeChild($span);
          }
          $classname = 'course_summary_image_wrapper';
          $spans = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
          for ($i = $spans->length - 1; $i > -1; $i--) {
            $span = $spans->item($i);
            $span->parentNode->removeChild($span);
          }
          /* if($GLOBALS['categoryid']){
            $summary = $dom->saveHTML($dom->documentElement);
            $coursescontent .= html_writer::start_tag('div',array('class'=>'categ-summary'));
            $coursescontent .= $summary;
            $coursescontent .= html_writer::end_tag('div');
          } */
          unset($dom);
          unset($spans);
          unset($a);
          
          $takecourselink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),get_string("takecourse",'theme_bootstrap'),array('class'=>'categ-button button-take-course'));
          
          $coursescontent .= html_writer::start_tag('span',array('class'=>'categ-actions'));
          $coursescontent .= $downloadlink;
          $coursescontent .= $takecourselink;
          $coursescontent .= html_writer::end_tag('span');
          
          
          
          $coursescontent .= html_writer::end_tag('li');
        }
        $coursescontent .= html_writer::end_tag('ul');
        
        $content .= $coursescontent;
      }  
      $content .= html_writer::end_tag('div'); // .category
      $chelper->set_show_courses($show_courses);
      // Return the course category tree HTML
      return $content;
    }
    public function course_category($category) {
          global $CFG, $OUTPUT;
          require_once($CFG->libdir. '/coursecatlib.php');
          $coursecat = coursecat::get(is_object($category) ? $category->id : $category);
          
          $site = get_site();
          
          if (can_edit_in_category($coursecat->id)) {
              // Add 'Manage' button if user has permissions to edit this category.
              $managebutton = $this->single_button(new moodle_url('/course/management.php',
                  array('categoryid' => $coursecat->id)), get_string('managecourses'), 'get');
              $this->page->set_button($managebutton);
          }
          if (!$coursecat->id) {
              if (coursecat::count_all() == 1) {
                  // There exists only one category in the system, do not display link to it
                  $coursecat = coursecat::get_default();
                  $strfulllistofcourses = get_string('fulllistofcourses');
                  $this->page->set_title("$site->shortname: $strfulllistofcourses");
              } else {
                  $strcategories = get_string('categories');
                  $this->page->set_title("$site->shortname: $strcategories");
              }
          } else {
              $title = $site->shortname;
              if (coursecat::count_all() > 1) {
                  $title .= ": ". $coursecat->get_formatted_name();
              }
              $this->page->set_title($title);
          }

          
          $chelper = new coursecat_helper();          
          $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT)
                ->set_attributes(array('class' => 'category-browse category-browse-'.$coursecat->id));
                
          $output = '';
          
          $output .= $OUTPUT->heading(html_writer::tag('span',html_writer::tag('span',get_string('ourcourses','theme_bootstrap'))),2,array('class'=>'full-bg'));
          
          $output .= html_writer::start_tag('div',array('class'=>'row categ-desc'));
          $output .= html_writer::start_tag('div',array('class'=>'col-lg-9 col-md-12 col-sm-12 col-xs-12 categ-desc2'));
          $output .= $this->coursecat_category($chelper,$coursecat,1);
          $output .= html_writer::end_tag('div');
          $output .= html_writer::start_tag('div',array('class'=>'col-lg-3 col-md-12 col-sm-12 col-xs-12 categ-other'));
          $output .= html_writer::start_tag('div',array('class'=>'box'));
          $output .= $OUTPUT->heading(get_string('othercourses','theme_bootstrap'),2,array('class'=>'box-title'));
          $output .= html_writer::start_tag('div',array('class'=>'box-content'));
          
          $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
          
          $output .= $this->coursecat_tree($chelper, coursecat::get(0));
          
          $output .= html_writer::end_tag('div');
          $output .= html_writer::end_tag('div');
          $output .= html_writer::end_tag('div');
          $output .= html_writer::end_tag('div');
          
          
          return $output;
      }
  }
