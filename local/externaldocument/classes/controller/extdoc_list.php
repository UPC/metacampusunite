<?php


namespace local_externaldocument\controller;

use context_system;
use moodle_url;
use table_sql;
use local_externaldocument\extdoc;
use stdClass;

class extdoc_list {

    private static $pageurl;
    private static $pagetitle;

    private static function set_pageurl($pageurl) {
        self::$pageurl = $pageurl;
    }

    private static function set_pagetitle($pagetitle) {
        self::$pagetitle = $pagetitle;
    }

    public static function setup_page($selectedtype) {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/lib/tablelib.php');

        $context = context_system::instance();
        $pageurl = new moodle_url('/local/externaldocument/test-list.php');
        $pagetitle = get_string('list_page_title', 'local_externaldocument');
        self::set_pageurl($pageurl);
        self::set_pagetitle($pagetitle);
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(self::$pagetitle);
        $PAGE->set_heading(self::$pagetitle);
        $PAGE->set_url(self::$pageurl);

        if (!empty($CFG->forcelogin)) {
            require_login();
        }
    }

    public static function render_page($selectedtype) {
        global $OUTPUT;

        echo $OUTPUT->header();
        $selector = new extdoc_typeselector($selectedtype);
        $selector->render_type_selector();
        $extdoc = extdoc::instance($selectedtype);
        self::render_table($extdoc);
        echo $OUTPUT->footer();
    }

    private static function render_table(extdoc $extdoc): void {
        global $DB, $OUTPUT;

        $renderdata = new stdClass();
        $tablename = $extdoc->get_table_name();
        $selectquery = $extdoc->get_select_query();
        $renderdata->headers = $extdoc->get_field_names();
        array_unshift($renderdata->headers, 'id');
        $renderdata->rows = $DB->get_records_select($tablename, '1 = 1', null, 'id DESC', $selectquery);
        sort($renderdata->rows);
        array_walk($renderdata->rows, function(&$row) use ($extdoc) {
            $row = array_values((array) $row);
            $url = new moodle_url('/local/externaldocument/delete.php', array('id' => $row[0], 'type' => $extdoc->get_extdoc_type()));
            $url = $url->out();
            $row[] = "<a href='$url' class='delete-extdoc'><i class='fa fa-trash'></i></a>";
        });
        echo $OUTPUT->render_from_template('local_externaldocument/extdoc_list', $renderdata);
    }

}