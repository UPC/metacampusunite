<?php

namespace local_externaldocument\output\form;

use local_externaldocument\extdoc;
use local_externaldocument\interfaces\extdoctype;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/externallib.php');

class course_add_resource extends \moodleform implements extdoctype {

    /**
     * Form definition.
     *
     * @return void
     */
    function definition() {
        global $USER, $DB, $OUTPUT;

        $mform =& $this->_form;
        $course = extdoc::instance('course');

        $mform->addElement('header', 'importheader', 'Manual input'); //#TODO# lang

        $mform->addElement('text', 'url', 'Url'); //#TODO# lang
        $mform->setType('url', $course->get_field_type('url'));
        $mform->addRule('url', 'An URL is required', 'required');

        $mform->addElement('text', 'title', 'Title'); //#TODO# lang
        $mform->setType('title', $course->get_field_type('title'));

        $mform->addElement('text', 'content', 'Content'); //#TODO# lang
        $mform->setType('content', $course->get_field_type('content'));

        $mform->addElement('text', 'author', 'Author'); //#TODO# lang
        $mform->setType('author', $course->get_field_type('author'));

        $mform->addElement('text', 'university', 'University'); //#TODO# lang
        $mform->setType('university', $course->get_field_type('university'));

        $mform->addElement('text', 'language', 'Language'); //#TODO# lang
        $mform->setType('language', $course->get_field_type('language'));

        $mform->addElement('text', 'tags', 'Tags'); //#TODO# lang
        $mform->setType('tags', $course->get_field_type('tags'));

        $mform->addElement('text', 'coursecredits', 'Course credits'); //#TODO# lang
        $mform->setType('coursecredits', $course->get_field_type('coursecredits'));

        $mform->addElement('date_time_selector', 'starttime', 'Start date', ['optional' => true]); //#TODO# lang
        $mform->setDefault('starttime', 0);

        $mform->addElement('date_time_selector', 'endtime', 'End date', ['optional' => true]); //#TODO# lang
        $mform->setDefault('endtime', 0);

        $this->add_action_buttons(false, 'Save'); //#TODO# lang
    }

    public function get_type(): string {
        return 'course';
    }
}
