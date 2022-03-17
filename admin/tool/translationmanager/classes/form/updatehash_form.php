<?php

namespace tool_translationmanager\form;

use moodleform;

defined('MOODLE_INTERNAL') || die;

class updatehash_form extends moodleform {
    function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'oldhash', 'Old hash');
        $mform->setType('oldhash', PARAM_TEXT);
        $mform->addElement('text', 'newhash', 'New hash');
        $mform->setType('newhash', PARAM_TEXT);

        $this->add_action_buttons();
    }

}