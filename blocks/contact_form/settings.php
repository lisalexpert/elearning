<?php
$site = get_site();
// echo '<pre>';
// print_r($site);
// die;
$settings->add(new admin_setting_configtext('block_contact_form_subject_prefix', get_string('subject_prefix', 'block_contact_form'),
                   get_string('subject_prefix_info', 'block_contact_form'), '['. strip_tags($site->shortname) .']',PARAM_RAW));

$settings->add(new admin_setting_configcheckbox('block_contact_form_receipt', get_string('receipt', 'block_contact_form'),
                   get_string('receipt_info', 'block_contact_form'), 0));
				   
$settings->add(new admin_setting_configtext('block_contact_form_email', get_string('email'),
                   get_string('subject_prefix_info', 'block_contact_form'), strip_tags($CFG->supportemail) .'',PARAM_RAW));
?>