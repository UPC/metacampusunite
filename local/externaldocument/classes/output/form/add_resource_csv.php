<?php

namespace local_externaldocument\output\form;

use core_search\manager;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->libdir . '/externallib.php');

class add_resource_csv extends \moodleform {

    /**
     * Form definition.
     *
     * @return void
     */
    function definition() {
        global $CFG, $USER, $DB, $OUTPUT;

        $mform =& $this->_form;
        $selectedtype = $this->_customdata['selectedtype'];

		$mform->addElement('header', 'uploadheader', 'Upload'); //#TODO# lang
		// $mform->addElement('static', 'user', $useracceptancelabel, join(', ', $usernames));


		$csvexamplefileurl = new \moodle_url('/local/externaldocument/examples/'.$selectedtype.'_externaldocument_example.csv');
		$link = \html_writer::link($csvexamplefileurl, $selectedtype.'_externaldocument_example.csv');
		$mform->addElement('static', 'examplecsv', 'Example file', $link); //#TODO# lang


		// $mform->addElement('file', 'attachment', 'Upload file'); //#TODO# lang


		$mform->addElement('filepicker', 'externaldocumentfile', get_string('file'));
		$mform->addRule('externaldocumentfile', null, 'required');


		$choices = \csv_import_reader::get_delimiter_list();
		$mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
		if (array_key_exists('cfg', $choices)) {
			$mform->setDefault('delimiter_name', 'cfg');
		} else if (get_string('listsep', 'langconfig') == ';') {
			$mform->setDefault('delimiter_name', 'semicolon');
		} else {
			$mform->setDefault('delimiter_name', 'comma');
		}

		$choices = \core_text::get_encodings();
		$mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
		$mform->setDefault('encoding', 'UTF-8');


		$this->add_action_buttons(false, 'Upload'); //#TODO# lang
    }
}
