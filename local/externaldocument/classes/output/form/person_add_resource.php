<?php

namespace local_externaldocument\output\form;

use local_externaldocument\extdoc;
use local_externaldocument\interfaces\extdoctype;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/externallib.php');

class person_add_resource extends \moodleform implements extdoctype {

    /**
     * Form definition.
     *
     * @return void
     */
    function definition() {
        global $USER, $DB, $OUTPUT;

        $mform =& $this->_form;
        $course = extdoc::instance('person');

        $mform->addElement('header', 'importheader', 'Manual input'); //#TODO# lang

        $mform->addElement('text', 'givenname', 'Given name'); //#TODO# lang
        $mform->setType('givenname', $course->get_field_type('givenname'));
        $mform->addRule('givenname', 'A given name is required', 'required');

        $mform->addElement('text', 'familyname', 'Family name'); //#TODO# lang
        $mform->setType('familyname', $course->get_field_type('familyname'));
        $mform->addRule('familyname', 'A family name is required', 'required');

        $mform->addElement('text', 'employer', 'Employer'); //#TODO# lang
        $mform->setType('employer', $course->get_field_type('employer'));

        $mform->addElement('text', 'tags', 'Tags'); //#TODO# lang
        $mform->setType('tags', $course->get_field_type('tags'));

        $mform->addElement('date_time_selector', 'employerstarttime', 'Employer start time', ['optional' => true]); //#TODO# lang
        $mform->setDefault('starttime', 0);

        $mform->addElement('date_time_selector', 'employerendtime', 'Employer end time', ['optional' => true]); //#TODO# lang
        $mform->setDefault('endtime', 0);

        $mform->addElement('text', 'url', 'URL'); //#TODO# lang
        $mform->setType('url', $course->get_field_type('url'));

        $mform->addElement('text', 'occupation', 'Occupation'); //#TODO# lang
        $mform->setType('occupation', $course->get_field_type('occupation'));

        $mform->addElement('text', 'orcidid', 'ORCID iD'); //#TODO# lang
        $mform->setType('orcidid', $course->get_field_type('orcidid'));

        $this->add_action_buttons(false, 'Save'); //#TODO# lang
    }

    public function get_type(): string {
        return 'person';
    }
}
