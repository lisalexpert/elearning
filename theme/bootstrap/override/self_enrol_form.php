<?php
require_once("$CFG->dirroot/enrol/self/locallib.php");
class enrol_self_enrol_quick_form extends enrol_self_enrol_form {
    protected function get_form_identifier() {
        $formid = $this->_customdata->id.'_enrol_self_enrol_form';
        return $formid;
    }
    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        $attributes = array(
            'action'=>new moodle_url('/enrol/index.php')
        );
        $mform->updateAttributes($attributes);
        $plugin = enrol_get_plugin('self');

        $heading = $plugin->get_instance_name($instance);
        $this->add_action_buttons(false, get_string('enrolme', 'enrol_self'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
    }
}