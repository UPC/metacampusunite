<?php

namespace tool_translationmanager\form;

use moodleform;

defined('MOODLE_INTERNAL') || die;

class updatecontent_form extends moodleform {
    function definition() {
        $mform = $this->_form;

        $mform->addElement('editor', 'oldtext', 'Old text');
        $mform->addElement('editor', 'newtext', 'New text');

        $mform->addElement('textarea', 'oldtextplain', 'Old text plain', 'wrap="virtual" rows="20" cols="50"');
        $mform->addElement('textarea', 'newtextplain', 'New text plain', 'wrap="virtual" rows="20" cols="50"');

        $this->add_action_buttons();
    }

}