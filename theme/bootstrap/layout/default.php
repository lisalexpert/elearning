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


$hassidepre = true;//$PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = true;//$PAGE->blocks->region_has_content('side-post', $OUTPUT);
$knownregionpre = true;//$PAGE->blocks->is_known_region('side-pre');
$knownregionpost = true;//$PAGE->blocks->is_known_region('side-post');
if(!isloggedin()){
	$hassidepre=false;
	$hassidepost=false;
	$knownregionpre=false;
	$knownregionpost=false;
}
$current_page = $PAGE->url->get_path();
$regions = bootstrap_grid($hassidepre, $hassidepost);
$PAGE->set_popup_notification_allowed(false);
if ($knownregionpre || $knownregionpost) {
    theme_bootstrap_initialise_zoom($PAGE);
}
$setzoom = theme_bootstrap_get_zoom();
if (isguestuser() or !isloggedin()) { 
  require_once($CFG->dirroot . '/auth/googleoauth2/lib.php');
  $auth_buttons = auth_googleoauth2_render_buttons();
}
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
    <?php echo $OUTPUT->standard_head_html(); ?>
	<link id="print" media="all" type="text/css" href="<?php echo $CFG->wwwroot.'/theme/'.$CFG->theme.'/style/print.css' ?>" rel="stylesheet" />
	<style>
	.disabled, .disabled:focus, .disabled:hover{
		background-color:transparent !important;
	}
	#toinformea{
		padding-right:0;
	}
	#toelearning{
		padding-left:0;
		margin-left:0;
		margin-top:15px;
	}
	@media (max-width: 990px){
		.navbar-nav{
			margin:0;
		}
		#toinformea{
			white-space:normal;
			position:relative;
		}
		#toelearning{
			white-space:normal;
			position:relative;
		}
	}
	@media (max-width: 480px){
		#toinformea{
			padding-bottom:0;
		}
		#toinformea img{
			height:25px;
			width:auto;
		}
		.elearning{
			padding-top:0;
			padding-bottom:0;
		}
		#toelearning{
			left: 12px;
			position: absolute;
			top: 12px;
		}
	}
	</style>
</head>

<body <?php echo $OUTPUT->body_attributes($setzoom); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<nav role="navigation" class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle btn" data-toggle="collapse" data-target="#moodle-navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a id="toinformea" class="navbar-brand" href="http://informea.org/">
			<img width="226" height="54" src="<?php echo $CFG->wwwroot;?>/informea_logo.png" alt="<?php echo $SITE->shortname; ?>" title="<?php echo $SITE->shortname; ?>" />
		</a>
        <a id="toelearning" class="navbar-brand" href="<?php echo $CFG->wwwroot;?>">
			<span class="elearningdot">•</span><span class="elearning">learning</span>
		</a>
    </div>

    <div id="moodle-navbar" class="navbar-collapse collapse">
        <?php echo $OUTPUT->custom_menu(); ?>
        <?php echo $OUTPUT->user_menu(); ?>
        <ul class="nav pull-right">
            <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
        </ul>
    </div>
  </div>
</nav>
<?php
if(!isloggedin() || isguestuser()){
  if(strpos($current_page,'/login') !== 0){
    global $SESSION;
    $SESSION->wantsurl = qualified_me();
  }
}
if($current_page == '/course/view.php'){ 
  $dom = new DOMDocument();
  $dom->loadHTML(format_text($this->page->course->summary,FORMAT_HTML,array('para'=>false)));
  $classname = 'course_summary_image_wrapper';
  $a = new DOMXPath($dom);
  $spans = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
  for ($i = $spans->length - 1; $i > -1; $i--) {
    echo file_rewrite_pluginfile_urls($spans->item($i)->C14N(), 'pluginfile.php', $PAGE->context->id, 'course', 'summary', null); 
    break;
  }
  unset($dom);
  unset($spans);
  unset($a);
}
?>
<header class="moodleheader">
    <div class="container">
		<div id="page-navbar" class="clearfix">
			<nav class="breadcrumb-nav" role="navigation" aria-label="breadcrumb"><?php echo $OUTPUT->navbar(); ?></nav>
		</div>
		<?php echo $OUTPUT->page_heading(); ?>
    </div>
</header>

<div id="page">
    <header id="page-header" class="clearfix container">
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
        <div id="top-right-corner">
			<div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
            <?php if ($knownregionpre || $knownregionpost) { ?>
                <div class="breadcrumb-button"> <?php echo $OUTPUT->content_zoom(); ?></div>
            <?php } ?>
        </div>
			
    </header>

    <div id="page-content" class="row">
        <div id="region-main" class="<?php echo $regions['content']; ?>">
			<div class="container" style="width:100%;max-width:1170px;">
				<?php
				echo $OUTPUT->course_content_header();
				echo $OUTPUT->main_content();
				echo $OUTPUT->course_content_footer();
				?>
			</div>
        </div>
	
        <?php
        if ($knownregionpre) {
            echo $OUTPUT->blocks('side-pre', $regions['pre']);
        }?>
        <?php
        if ($knownregionpost) {
            echo $OUTPUT->blocks('side-post', $regions['post']);
        }?>
    </div>

    <footer id="page-footer">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
<?php
if (isguestuser() or !isloggedin()) { 
?>
<div id="signup-login-popup-wrapper">
  <div id="signup-login-popup-container">
    <div id="signup-login-popup">
      <div id="signup-login-content">
        <div id="signup-login">
          <input class="signup-login-radios" id="signup-login-type-up" type="radio" name="sign-type" value="up" checked="checked"/>
          <input class="signup-login-radios" id="signup-login-type-in" type="radio" name="sign-type" value="in"/>
          <div id="signup-login-buttons">
            <label id="signup-login-type-for-up" for="signup-login-type-up" class="signup-login-button"><?php echo get_string('signup','theme_bootstrap'); ?></label>
            <label id="signup-login-type-for-in" for="signup-login-type-in" class="signup-login-button"><?php echo get_string('login','theme_bootstrap'); ?></label>
          </div>
          <div id="signup-login-containers">
            <div id="signup-type-up-container" class="signup-login-container">
              <h2><?php echo get_string('registerheader','theme_bootstrap'); ?></h2>
              <?php $form_action = new moodle_url('/login/signup.php'); ?>
              <form id="signup-login-signup-form" action="<?php echo $form_action; ?>" method="post" accept-charset="utf-8" autocomplete="off" />
                <label id="signup-login-for-firstname" for="signup-login-firstname">
                  <input maxlength="100" size="30" name="firstname" type="text" id="signup-login-firstname" placeholder="<?php echo get_string('firstnamereq','theme_bootstrap'); ?>" required="required" />
                </label>
                <label id="signup-login-for-lastname" for="signup-login-lastname">
                  <input maxlength="100" size="30" name="lastname" type="text" id="signup-login-lastname" placeholder="<?php echo get_string('lastnamereq','theme_bootstrap'); ?>" required="required" />
                </label>
                <label id="signup-login-for-email" for="signup-login-email">
                  <input maxlength="100" size="25" name="email" type="text" id="signup-login-email" class="text-ltr" placeholder="<?php echo get_string('emailreq','theme_bootstrap'); ?>" required="required" />
                </label>
                <label id="signup-login-for-password" for="signup-login-password">
                  <input type="password" name="password" id="signup-login-password" value="" data-size="12" size="12" placeholder="<?php echo get_string('setpassreq','theme_bootstrap'); ?>" required="required"/>
                </label>
                <button name="submitbutton" type="submit"><?php echo get_string('createyouraccount','theme_bootstrap'); ?></button>
                <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
                <input type="hidden" name="profile_field_gender" value="Male" />
                <input type="hidden" name="email2" value="" />
                <input type="hidden" name="username" value="" />
<?php /*
                <input type="hidden" name="city" value="" />
                <input type="hidden" name="country" value="" />
*/ ?>
                <input type="hidden" name="_qf__login_signup_form" value="1" />
              </form>
            </div>
            <div id="signup-type-in-container" class="signup-login-container">
              <h2><?php echo get_string('loginheader','theme_bootstrap'); ?></h2>
              <?php $form_action = new moodle_url('/login/index.php'); ?>
              <form id="signup-login-signin-form" action="<?php echo $form_action; ?>" method="post" accept-charset="utf-8" autocomplete="off" />
                <label id="signup-login-for-username" for="signup-login-username">
                  <input maxlength="100" size="30" name="username" type="text" id="signup-login-username" placeholder="<?php echo get_string('usernamereq','theme_bootstrap'); ?>" required="required" />
                </label>
                <label id="signup-login-for-password2" for="signup-login-password2">
                  <input type="password" name="password" id="signup-login-password2" value="" data-size="12" size="12" placeholder="<?php echo get_string('passreq','theme_bootstrap'); ?>" required="required" />
                </label>
                <button type="submit"><?php echo get_string('login','theme_bootstrap'); ?></button>
                <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
              </form>
            </div>
          </div>
        </div>
        <span id="signup-login-social-hr-divider"><?php echo get_string('or','theme_bootstrap'); ?></span>
        <hr />
        <div id="signup-login-social-container">
          <h2><?php echo get_string('registersocialheader','theme_bootstrap'); ?></h2>
        <?php
        echo $auth_buttons;
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php  
}
?>
<?php include(__DIR__ .'/footer.php') ?>
<script>
var addCustomTinyMCEOptions =function(options){
  if(typeof options.template_templates === 'undefined'){
    options.template_templates = [];
  }
  if(typeof options.template_replace_values === 'undefined'){
    options.template_replace_values = {};
  }
  if(typeof options.content_css === 'undefined'){
    options.content_css = "";
  } else {
    options.content_css += ",";
  }
  options.content_css += "theme/styles_debug.php?sheet=moodle&theme=bootstrap&type=theme,theme/styles_debug.php?sheet=informea&theme=bootstrap&type=theme,theme/styles_debug.php?sheet=font-awesome&theme=bootstrap&type=theme,theme/styles_debug.php?sheet=informea2&theme=bootstrap&type=theme,theme/styles_debug.php?sheet=informea3&theme=bootstrap&type=theme";
<?php if($current_page == '/course/edit.php'){ ?>
    options.template_templates.push({
     "title": "Course Summary - Flyer",
     "src": "lib/editor/tinymce/tiny_mce/3.5.11/plugins/template/templates/course_summary_flyer.html"
    });
    options.template_templates.push({
     "title": "Course Summary - Course Image",
     "src": "lib/editor/tinymce/tiny_mce/3.5.11/plugins/template/templates/course_summary_course_image.html"
    });
    var category_name = $('#id_category option:selected').text();
    $('#id_category').bind('change',function(){$('#id_category option:selected').text()});
    options.template_replace_values.category_name = category_name;
    
    var course_name = $('#id_fullname').val();
    $('#id_fullname').bind('change',function(){course_name = this.value});
    options.template_replace_values.course_name = course_name;
<?php } ?>
<?php if($current_page == '/course/editsection.php'){ ?>
    options.template_templates.push({
     "title": "Course Section Template",
     "src": "lib/editor/tinymce/tiny_mce/3.5.11/plugins/template/templates/course_section_template.html"
    });
    options.template_templates.push({
     "title": "Course Section Element",
     "src": "lib/editor/tinymce/tiny_mce/3.5.11/plugins/template/templates/course_section_element.html"
    });
    options.template_templates.push({
     "title": "Course Section Elements Template",
     "src": "lib/editor/tinymce/tiny_mce/3.5.11/plugins/template/templates/course_section_elements.html"
    });
<?php } ?>
<?php if($current_page == '/course/view.php' && isset($_GET['bui_editid'])){ ?>
    options.template_templates.push({
     "title": "Course Section Element",
     "src": "lib/editor/tinymce/tiny_mce/3.5.11/plugins/template/templates/course_section_element.html"
    });
<?php } ?>
}

$(document).ready(function(){
<?php if(isguestuser() or !isloggedin()){ ?>
  var cust_user_ref_id = $('#cust-user-ref-id');
  if(cust_user_ref_id.length){
    
    cust_user_ref_id.parent().addClass('navbar-quicklogin');
  }
<?php } ?>
  $('#block-region-side-pre').on('click','.block>.header>.title>h2',function(){
      $(this).closest('.block').toggleClass('is_opened');
  });
<?php if($current_page == '/course/view.php' && (isguestuser() or !isloggedin())){ ?>
  $('#signup-login-signup-form').on('submit',function(){
    this.email2.value = this.email.value;
    this.username.value = this.email.value;
    this.submit();
    return false;
  });
  $('#signup-login-popup-wrapper').toggleClass('opened');
<?php } ?>
  jQuery('img.replace-svg').each(function(){
    var $img = jQuery(this);
    var imgID = $img.attr('id');
    var imgClass = $img.attr('class');
    var imgURL = $img.attr('src');
    if(imgURL.indexOf('.svg') === false){
      return;
    }

    jQuery.get(imgURL, function(data) {
      var $svg = jQuery(data).find('svg');
      if(typeof imgID !== 'undefined') {
        $svg = $svg.attr('id', imgID);
      }
      if(typeof imgClass !== 'undefined') {
        $svg = $svg.attr('class', imgClass+' replaced-svg');
      }
      $svg = $svg.removeAttr('xmlns:a');
      $img.replaceWith($svg);
    }, 'xml');
  });
});
</script>
</body>
</html>
