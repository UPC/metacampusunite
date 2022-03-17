<?php

use tool_translationmanager\form\translation_form;

require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir.'/adminlib.php');
require_login();
require_capability('filter/fulltranslate:edittranslations', context_system::instance());
$url = new moodle_url('/admin/tool/edit.php');
$PAGE->set_url($url);
$PAGE->set_title('Full translate filter');
$id = required_param('id', PARAM_INT);
$pagefilter = optional_param('pagefilter', '', PARAM_TEXT);
$lang = optional_param('searchlang', '', PARAM_ALPHA);
$record = $DB->get_record('filter_fulltranslate', ['id' => $id]);
$mform = new translation_form(null, ['lang' => $lang, 'pagefilter' => $pagefilter, 'textformat' => $record->textformat]);
if ($record->textformat == 'html') {
    $record->translation = ['text' => $record->translation];
}
$record->length = strlen($record->sourcetext);
$record->searchlang = $lang;
$record->pagefilter = urlencode($pagefilter);
$mform->set_data($record);
$params = [];
if ($fromform = $mform->get_data()) {
    if ($record->textformat == 'html') {
        $record->translation = $fromform->translation['text'];
    } else {
        $record->translation = $fromform->translation;
    }

    $record->timemodified = time();
    $record->automatic = 0;
    $DB->update_record('filter_fulltranslate', $record);
    if ($pagefilter) {
        $params['pagefilter'] = urldecode($pagefilter);
    }
    if ($lang) {
        $params['searchlang'] = $lang;
    }
    redirect(new moodle_url('/admin/tool/translationmanager/index.php', $params), get_string('success'), 4);
}
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();