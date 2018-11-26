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
    <script type="text/javascript">
        window.smartlook||(function(d) {
            var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
            var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
            c.charset='utf-8';c.src='https://rec.smartlook.com/recorder.js';h.appendChild(c);
        })(document);
        smartlook('init', '0de702f6d7de31817c785d9aa3f47f68815193da');
    </script>
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
	
	<?php
	$chelper = new coursecat_helper();
	$css = "";
	require_once($CFG->libdir . '/coursecatlib.php');
	require_once($CFG->libdir . '/filelib.php');
	foreach (coursecat::make_categories_list() as $cat_id => $cat_name) {
        $coursecat = coursecat::get($cat_id);
        $categorycontent = strip_tags($chelper->get_category_formatted_description($coursecat),'<img><span>');
      
        if($categorycontent) {
            $doc = new DOMDocument();
            $doc->loadHTML($categorycontent);    
            $selector = new DOMXPath($doc);
            
            // Modify only new categories.
            $span = $selector->query('//span')[0];
            if ($span) {
                $color = explode(": ", $span->getAttribute('style'))[1];
                $color = rtrim($color, ";");
                
                $css .= "
#frontpage-category-names .category-{$coursecat->id} > a > .box > .content,
#frontpage-category-combo .category-{$coursecat->id} > a > .box > .content,
.category-{$coursecat->id} .numberofcourse, div.category-{$coursecat->id} .numberofcourse {background-color: {$color};}
#frontpage-category-combo .category-{$coursecat->id} .categ-courses>li:before {color: {$color};}
.category-{$coursecat->id} .categ-button {color: {$color};}
#page-course-index-category .category-{$coursecat->id} .svg-content2>svg path {fill: {$color} !important;}
#page-course-index-category .category-{$coursecat->id} .categoryname {color: {$color};}";
            }
        }
	}
	echo $css;
	?>
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
        <span id="signup-button-close">
          <i class="fa fa-times"></i>
        </span>
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
  window.mobilecheck = function() {
    var check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
  };
	
  <?php if(isguestuser() or !isloggedin()){ ?>
  var cust_user_ref_id = $('#cust-user-ref-id');
  if(cust_user_ref_id.length){
    
    cust_user_ref_id.parent().addClass('navbar-quicklogin');

    cust_user_ref_id.parent().click(function() {
        if (!mobilecheck())
        {
           window.location.href = cust_user_ref_id.parent().attr("href");
        }
    });
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
  $('#page-content').on('click','a[href]',function(e){
    event.preventDefault();
    event.stopPropagation();
    $('#signup-login-popup-wrapper').toggleClass('opened');
    return false;
  });
  $('#signup-button-close').on('click', function(e){
    $('#signup-login-popup-wrapper').toggleClass('opened');
  });
  $('#signup-login-popup-wrapper').on('click', function(e){
    if(e.target !== this)
      return;
    $('#signup-login-popup-wrapper').toggleClass('opened');
  });
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
