<?php

require_once(__DIR__ . '/../../config.php');

use local_externaldocument\controller\extdoc_create,
    local_externaldocument\output\form\add_resource_csv;

$selectedtype = optional_param('type', 'course', PARAM_TEXT);
extdoc_create::setup_page($selectedtype);

$formclass = '\local_externaldocument\output\form\\'.$selectedtype.'_add_resource';
$currenturl = new moodle_url($PAGE->url, array('type' => $selectedtype));
$mform = new $formclass($currenturl);
$mformcsv = new add_resource_csv($currenturl, ['selectedtype' => $selectedtype]);

if ($mformcsv->is_submitted() && $csvformdata = $mformcsv->get_data()) {
    $filecontent = $mformcsv->get_file_content('externaldocumentfile');
    extdoc_create::process_csv_form($csvformdata, $filecontent, $mform->get_type());
    redirect($currenturl, 'Saved!', null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($mform->is_submitted() && $manualformdata = $mform->get_data()) {
	extdoc_create::process_manual_form($manualformdata, $mform->get_type());
    redirect($currenturl, 'Saved!', null, \core\output\notification::NOTIFY_SUCCESS);
}

extdoc_create::render_page($mformcsv, $mform, $selectedtype);