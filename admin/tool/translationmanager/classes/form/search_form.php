<?php

namespace tool_translationmanager\form;

use moodleform;

defined('MOODLE_INTERNAL') || die;

class search_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'search', get_string('search'));
        $mform->setType('search', PARAM_TEXT);
        $this->add_action_buttons(false, 'Search');
    }
}