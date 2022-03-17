<?php

namespace local_externaldocument\output\form;

use local_externaldocument\extdoc;
use local_externaldocument\interfaces\extdoctype;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/externallib.php');

class webpage_add_resource extends \moodleform implements extdoctype {

    /**
     * Form definition.
     *
     * @return void
     */
    function definition() {
        global $USER, $DB, $OUTPUT;

        $mform =& $this->_form;
        $course = extdoc::instance('webpage');

        $mform->addElement('header', 'importheader', 'Manual input'); //#TODO# lang

        $mform->addElement('text', 'url', 'Url'); //#TODO# lang
        $mform->setType('url', $course->get_field_type('url'));
        $mform->addRule('url', 'An URL is required', 'required');

        $mform->addElement('text', 'title', 'Label'); //#TODO# lang
        $mform->setType('title', $course->get_field_type('title'));

        $mform->addElement('text', 'language', 'Language'); //#TODO# lang
        $mform->setType('language', $course->get_field_type('language'));

        $mform->addElement('text', 'languageofwork', 'Language of work'); //#TODO# lang
        $mform->setType('languageofwork', $course->get_field_type('language'));

        $mform->addElement('text', 'description1', 'Description'); //#TODO# lang
        $mform->setType('description1', $course->get_field_type('description1'));

        $mform->addElement('text', 'tags', 'Tags'); //#TODO# lang
        $mform->setType('tags', $course->get_field_type('tags'));

        $this->add_action_buttons(false, 'Save'); //#TODO# lang
    }

    public function get_type(): string {
        return 'webpage';
    }
}
