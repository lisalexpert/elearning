<?php
require_once("../../config.php");
$context = context_system::instance();
require_login(0, false);
require_capability('moodle/site:config', $context);

$tables = array(
	'assign' => array('name', 'intro'),
	'assignfeedback_comments' => array('commenttext'),
	'assignfeedback_editpdf_annot' => array('path'),
	'assignfeedback_editpdf_cmnt' => array('rawtext'),
	'assignfeedback_editpdf_quick' => array('rawtext'),
	'assignment' => array('name', 'intro'),
	'assignment_submissions' => array('data1', 'data2', 'submissioncomment'),
	'assignsubmission_onlinetext' => array('onlinetext'),
	'assign_plugin_config' => array('value'),
	'backup_controllers' => array('controller'),
	'backup_logs' => array('message'),
	'badge' => array('name', 'description', 'message', 'messagesubject'),
	'badge_issued' => array('uniquehash'),
	'block_community' => array('coursename', 'coursedescription'),
	'block_configurable_reports' => array('summary', 'components'),
	'block_instances' => array('configdata'),
	'block_rss_client' => array('title', 'description'),
	'blog_external' => array('name', 'description', 'url'),
	'book' => array('name', 'intro'),
	'book_chapters' => array('title', 'content'),
	'cache_filters' => array('rawtext'),
	'cache_flags' => array('value'),
	'certificate' => array('name', 'intro', 'emailothers', 'customtext'),
	'chat' => array('name', 'intro'),
	'chat_messages' => array('message'),
	'chat_messages_current' => array('message'),
	'choice' => array('name', 'intro'),
	'choice_options' => array('text'),
	'cohort' => array('name', 'description'),
	'comments' => array('commentarea', 'content'),
	'config' => array('value'),
	'config_log' => array('value', 'oldvalue'),
	'config_plugins' => array('value'),
	'course' => array('fullname', 'summary'),
	'course_categories' => array('name', 'description'),
	'course_format_options' => array('value'),
	'course_modules' => array('availability'),
	'course_request' => array('fullname', 'summary', 'reason'),
	'course_sections' => array('name', 'summary', 'sequence', 'availability'),
	'data' => array('name', 'intro', 'singletemplate', 'listtemplate', 'listtemplateheader', 'listtemplatefooter', 'addtemplate', 'rsstemplate', 'rsstitletemplate', 'csstemplate', 'jstemplate', 'asearchtemplate'),
	'data_content' => array('content', 'content1', 'content2', 'content3', 'content4'),
	'data_fields' => array('name', 'description', 'param1', 'param2', 'param3', 'param4', 'param5', 'param6', 'param7', 'param8', 'param9', 'param10'),
	'enrol' => array('name', 'customtext1', 'customtext2', 'customtext3', 'customtext4'),
	'event' => array('name', 'description'),
	'events_handlers' => array('handlerfunction'),
	'events_queue' => array('eventdata', 'stackdump'),
	'events_queue_handlers' => array('errormessage'),
	'feedback' => array('name', 'intro', 'page_after_submit'),
	'feedback_item' => array('name', 'presentation'),
	'feedback_template' => array('name'),
	'feedback_value' => array('value'),
	'feedback_valuetmp' => array('value'),
	'files' => array('source'),
	'files_reference' => array('reference'),
	'filter_config' => array('value'),
	'folder' => array('name', 'intro'),
	'forum' => array('name', 'intro'),
	'forum_discussions' => array('name'),
	'forum_posts' => array('subject', 'message'),
	'game' => array('name', 'param9', 'toptext', 'bottomtext'),
	'game_course' => array('messagewin', 'messageloose'),
	'game_course_inputs' => array('ids'),
	'game_cryptex' => array('letters'),
	'game_queries' => array('questiontext', 'studentanswer', 'answertext'),
	'game_snakes_database' => array('data'),
	'glossary' => array('intro'),
	'glossary_entries' => array('definition'),
	'grade_categories' => array('fullname'),
	'grade_grades' => array('feedback', 'information'),
	'grade_grades_history' => array('feedback', 'information'),
	'grade_import_values' => array('feedback'),
	'grade_items' => array('itemname', 'iteminfo', 'calculation'),
	'grade_items_history' => array('itemname', 'iteminfo', 'calculation'),
	'grade_outcomes' => array('fullname', 'description'),
	'grade_outcomes_history' => array('fullname', 'description'),
	'grade_settings' => array('value'),
	'gradingform_guide_comments' => array('description'),
	'gradingform_guide_criteria' => array('description', 'descriptionmarkers'),
	'gradingform_guide_fillings' => array('remark'),
	'gradingform_rubric_criteria' => array('description'),
	'gradingform_rubric_fillings' => array('remark'),
	'gradingform_rubric_levels' => array('definition'),
	'grading_definitions' => array('name', 'description', 'options'),
	'grading_instances' => array('feedback'),
	'groupings' => array('name', 'description', 'configdata'),
	'groups' => array('description'),
	'imscp' => array('name', 'intro', 'structure'),
	'label' => array('name', 'intro'),
	'learning_learningplan' => array('learning_plan', 'description'),
	'learning_traningtype' => array('description'),
	'lesson' => array('name', 'conditions'),
	'lesson_answers' => array('answer', 'response'),
	'lesson_attempts' => array('useranswer'),
	'lesson_pages' => array('title', 'contents'),
	// 'license' => array('fullname'),
	// 'logstore_standard_log' => array('other'),
	// 'log_queries' => array('sqltext', 'sqlparams', 'info', 'backtrace'),
	'lti' => array('name', 'intro', 'toolurl', 'securetoolurl', 'icon', 'secureicon'),
	'lti_types' => array('name', 'baseurl'),
	'message' => array('subject', 'fullmessage', 'fullmessagehtml', 'smallmessage', 'contexturl', 'contexturlname'),
	'message_read' => array('subject', 'fullmessage', 'fullmessagehtml', 'smallmessage', 'contexturl', 'contexturlname'),
	'mnetservice_enrol_courses' => array('summary'),
	'mnet_host' => array('public_key'),
	'mnet_rpc' => array('help', 'profile'),
	'page' => array('name', 'intro', 'content', 'displayoptions'),
	'portfolio_instance' => array('name'),
	'portfolio_instance_config' => array('value'),
	'portfolio_instance_user' => array('value'),
	'portfolio_tempdata' => array('data'),
	'post' => array('summary', 'content'),
	'profiling' => array('data'),
	'qtype_essay_options' => array('graderinfo', 'responsetemplate'),
	'qtype_match_options' => array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'),
	'qtype_match_subquestions' => array('questiontext', 'answertext'),
	'qtype_multichoice_options' => array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'),
	'qtype_randomsamatch_options' => array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'),
	'question' => array('name', 'questiontext', 'generalfeedback'),
	'question_answers' => array('answer', 'feedback'),
	'question_attempts' => array('questionsummary', 'rightanswer', 'responsesummary'),
	'question_attempt_step_data' => array('value'),
	'question_calculated_options' => array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'),
	'question_categories' => array('name', 'info'),
	'question_hints' => array('hint'),
	'question_multianswer' => array('sequence'),
	'question_response_analysis' => array('response'),
	'question_statistics' => array('subquestions', 'positions'),
	'quiz' => array('name', 'intro'),
	'quiz_attempts' => array('layout'),
	'quiz_feedback' => array('feedbacktext'),
	'repository_instances' => array('name'),
	'repository_instance_config' => array('value'),
	'resource' => array('name', 'intro', 'displayoptions'),
	'resource_old' => array('intro', 'alltext', 'popup'),
	'role' => array('name', 'description'),
	'role_names' => array('name'),
	'scale' => array('name', 'scale', 'description'),
	'scale_history' => array('scale', 'description'),
	'scorm' => array('name', 'intro'),
	'scorm_scoes' => array('launch'),
	'scorm_scoes_data' => array('value'),
	'scorm_scoes_track' => array('value'),
	'sessions' => array('sessdata'),
	'survey' => array('name', 'intro'),
	'survey_analysis' => array('notes'),
	'survey_answers' => array('answer1', 'answer2'),
	'survey_questions' => array('options'),
	'tag' => array('description'),
	'tag_correlation' => array('correlatedtags'),
	'task_adhoc' => array('customdata'),
	// 'tool_customlang' => array('original', 'master', 'local'),
	// 'upgrade_log' => array('details', 'backtrace'),
	'url' => array('parameters', 'displayoptions', 'externalurl', 'intro', 'name'),
	'user' => array('description'),
	'user_info_data' => array('data'),
	'user_info_field' => array('param5', 'param4', 'param3', 'param2', 'param1', 'defaultdata', 'description', 'name'),
	'wiki' => array('intro', 'name'),
	'wiki_pages' => array('cachedcontent'),
	'wiki_versions' => array('content'),
	'workshop' => array('conclusion', 'instructreviewers', 'instructauthors', 'intro', 'name'),
	'workshopallocation_scheduled' => array('resultlog', 'settings'),
	'workshopform_accumulative' => array('description'),
	'workshopform_comments' => array('description'),
	'workshopform_numerrors' => array('description'),
	'workshopform_rubric' => array('description'),
	'workshopform_rubric_levels' => array('definition'),
	'workshop_assessments' => array('feedbackauthor', 'feedbackreviewer'),
	'workshop_assessments_old' => array('generalcomment', 'teachercomment'),
	'workshop_comments_old' => array('comments'),
	'workshop_elements_old' => array('description'),
	'workshop_grades' => array('peercomment'),
	'workshop_grades_old' => array('feedback'),
	'workshop_old' => array('description'),
	'workshop_rubrics_old' => array('description'),
	'workshop_stockcomments_old' => array('comments'),
	'workshop_submissions' => array('feedbackauthor', 'content', 'title'),
	'workshop_submissions_old' => array('description'),
);
global $CFG, $DB;
$dbman = $DB->get_manager();
foreach($tables as $tableName=>$fields){
	if (! $dbman->table_exists($tableName)) {
		echo "Table ".$tableName." does not exist\n";
		continue;
	}
	$sql = 'SELECT `'.implode('`,`',$fields).'`
		FROM {'.$tableName.'} c
	';
	$results = $DB->get_records_sql($sql);
	foreach ($results as $key => $result) {
		foreach($fields as $field){
			if(!$result->$field) continue;
			if(strpos($result->$field,'mlang')!==false){
				$text = format_text($result->$field, FORMAT_HTML, array('trusted' => true,'noclean' => true, 'para' => false));
				if(strpos($text,'mlang')!==false){
					echo "IN Table ".$tableName."<pre>".$result->$field."</pre>\n";
				}
			}
		}
	}
}