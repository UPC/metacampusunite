<?php

namespace local_externaldocument\controller;

use csv_import_reader;
use context_system;
use local_externaldocument\extdoc;
use moodle_url;
use core\notification;

class extdoc_create {

    public static function setup_page($selectedtype) {
        global $PAGE, $CFG;

        $context = context_system::instance();
        $pagetitle = get_string('globalsearch', 'search');
        $pageurl = new moodle_url('/local/externaldocument/test-create.php');
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pagetitle);
        $PAGE->set_url($pageurl);

        if (!empty($CFG->forcelogin)) {
            require_login();
        }
    }

    public static function render_page($mformcsv, $mform, $selectedtype) {
        global $OUTPUT;

        $pagetitle = get_string('create_page_title', 'local_externaldocument');

        echo $OUTPUT->header();
        $selector = new extdoc_typeselector($selectedtype);
        $selector->render_type_selector();
        echo $OUTPUT->heading($pagetitle);

        $mformcsv->display();
        $mform->display();

        echo $OUTPUT->footer();

    }

    public static function process_csv_form($csvformdata, $filecontent, $type) {
        global $PAGE;

        $iid = csv_import_reader::get_new_iid('externaldocumentfile');
        $cir = new csv_import_reader($iid, 'externaldocumentfile');

        $cir->load_csv_content($filecontent, $csvformdata->encoding, $csvformdata->delimiter_name);
        $csvloaderror = $cir->get_error();

        if (!is_null($csvloaderror)) {
            print_error('csvloaderror', '', $PAGE->url, $csvloaderror);
        }

        $keys = $cir->get_columns();
        $cir->init();
        $timemodified = time();

        while ($row_values = $cir->next()) {
            //#TODO# timemodified si el procés dura molt i s'executa el cron mentrestant
            //#TODO# posar timemodified al futur perqué així no es pugui solapar?
            $row = (object) array_combine($keys, $row_values);
            $row->timemodified = $timemodified;

            $extdoc = extdoc::instance($type);
            $extdoc->parse_csv_dates($row);
            $extdoc->add($row);
        }
    }

    public static function process_manual_form($manualformdata, string $type) {
        $manualformdata->timemodified = time();

        $extdoc = extdoc::instance($type);
        $extdoc->add($manualformdata);
    }

    private static function parse_csv_dates($row) {
        $row->starttime = strtotime(str_replace('/', '-', $row->starttime));
        $row->endtime = strtotime(str_replace('/', '-', $row->endtime));
    }

}