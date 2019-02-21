<?php
class block_diplomas_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
        
        $mform->addElement('text', 'config_title', get_string('blockstring', 'block_diplomas'));
        $mform->setDefault('config_title', get_string('pluginname', 'block_diplomas'));
        $mform->setType('config_title', PARAM_TEXT); 
    }
}
