<?php
function xmldb_filter_multilangsecond_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

	$savepoint = 2015122100;
	if ($oldversion < $savepoint) {
		// change the names from varchar to text
		// list of all the tables that need the name changed
		$tables = array(
			'assign'=>array('name'),
			'assignment'=>array('name'),
			'badge'=>array('name'),
			'block_community'=>array('coursename'),
			'blog_external'=>array('name'),
			'book'=>array('name'),
			'book_chapters'=>array('title'),
			'chat'=>array('name'),
			'choice'=>array('name'),
			'cohort'=>array('name'),
			'comments'=>array('commentarea'),
			'certificate'=>array('name'),
			'course'=>array('fullname'),
			'course_categories'=>array('name'),
			'course_request'=>array('fullname'),
			'course_sections'=>array('name'),
			'data'=>array('name'),
			'data_fields'=>array('name'),
			'enrol'=>array('name'),
			'feedback'=>array('name'),
			'feedback_item'=>array('name'),
			'feedback_template'=>array('name'),
			'folder'=>array('name'),
			'forum'=>array('name'),
			'forum_discussions'=>array('name'),
			'forum_posts'=>array('subject'),
			'game'=>array('name'),
			// 'glossary'=>array('name'),
			'grade_categories'=>array('fullname'),
			'grade_items'=>array('itemname'),
			'grade_items_history'=>array('itemname'),
			'grading_definitions'=>array('name'),
			'groupings'=>array('name'),
			'imscp'=>array('name'),
			'label'=>array('name'),
			'learning_learningplan'=>array('learning_plan'),
			// 'learning_training'=>array('learning_training'),
			'lesson'=>array('name'),
			'lesson_pages'=>array('title'),
			'lti'=>array('name'),
			'lti_types'=>array('name'),
			'page'=>array('name'),
			'portfolio_instance'=>array('name'),
			'repository_instances'=>array('name'),
			'resource'=>array('name'),
			'role'=>array('name'),
			'role_names'=>array('name'),
			'scale'=>array('name'),
			'scorm'=>array('name'),
			'survey'=>array('name'),
			'qtype_match_subquestions'=>array('answertext'),
			'question'=>array('name'),
			'question_categories'=>array('name'),
			'quiz'=>array('name'),
			'url'=>array('name'),
			'wiki'=>array('name'),
			'workshop'=>array('name'),
			'workshop_submissions'=>array('title'),
		);
		
		foreach($tables as $tableName=>$fields){
			if ($dbman->table_exists($tableName)) {
				$table = new xmldb_table($tableName);
				foreach($fields as $fieldName){
					$field = new xmldb_field($fieldName);
					$field->set_attributes(XMLDB_TYPE_TEXT, null, null, null, null,null);
					$dbman->change_field_type($table, $field);
				}
			}
		}
		upgrade_plugin_savepoint(true, $savepoint, 'filter', 'multilangsecond');
	}
    return true;
}
