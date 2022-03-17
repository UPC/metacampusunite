<?php

namespace tool_translationmanager\form;

use moodleform;
use tool_wp\modal_form;

defined('MOODLE_INTERNAL') || die;

class translation_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('html', '<div class="row"><div class="col-6">');
        $mform->addElement('static', 'sourcetext', get_string('sourcetext', 'tool_translationmanager'));
        $mform->addElement('static', 'hashkey', 'Hash');

        $mform->addElement('html', '</div><div class="col-6">');
        if ($this->_customdata['textformat'] == 'plain') {
            $mform->addElement('textarea', 'translation', '', 'wrap="virtual" rows="20" cols="50"');
        } else {
            $options = [];
            if (isset($this->_customdata['length'])) {
                $rows = round(($this->_customdata['length'] / 42));
                $options['rows'] = $rows;
            }
            $mform->addElement('editor', 'translation', null, $options);
        }
        $mform->addElement('hidden', 'lang');
        $mform->setType('lang', PARAM_ALPHA);
        $mform->setType('searchlang', PARAM_ALPHA);
        $mform->addElement('hidden', 'pagefilter');
        $mform->setType('pagefilter', PARAM_ALPHA);
        $mform->addElement('html', '</div></div><div class="row"><div class="col-3">');
        $mform->addElement('button', 'reset', get_string('reset'));
        $mform->addElement('html', '</div><div class="col-3"></div><div class="col-6">');
        $this->add_action_buttons();
        $mform->addElement('html', '</div>');
    }

    public function process(\stdClass $data) {

    }


    public function require_access() {
        require_capability('filter/fulltranslate:edittranslations', \context_system::instance()->id);
    }
}