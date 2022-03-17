<?php

namespace local_externaldocument\search;

use core_search\base_mod,
    context,
    core_search\document_factory,
    core_search\manager,
    core_search\document,
    moodle_url;

defined('MOODLE_INTERNAL') || die();

class person extends base_mod {

    public function get_document_recordset($modifiedfrom = 0, context $context = null) {
        global $DB;

        $sql = "SELECT *
                FROM {extdoc_t_person}
                WHERE timemodified >= ? 
                ORDER BY timemodified ASC";

        return $DB->get_recordset_sql($sql, array($modifiedfrom));
    }

    public function get_document($record, $options = array()) {
        $doc = document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->title, false));
        $doc->set('content', content_to_text($record->content, false));
        $doc->set('description1', content_to_text($record->description1, false));
        $doc->set('description2', content_to_text($record->description2, false));
        $doc->set('givenname', $record->givenname);
        $doc->set('familyname', $record->familyname);
        $doc->set('employer', $record->employer);
        if (isset($record->employerstarttime)) {
            $doc->set('employerstarttime', $record->employerstarttime);
        }
        if (isset($record->employerendtime)) {
            $doc->set('employerendtime', $record->employerendtime);
        }
        $doc->set('tags', $record->tags);
        $doc->set('url', $record->url);
        $doc->set('occupation', $record->occupation);
        $doc->set('orcidid', $record->orcidid);
        $doc->set('modified', $record->timemodified);
        $doc->set('owneruserid', manager::NO_OWNER_ID);
        $doc->set('contextid', 1); //system
        $doc->set('courseid', 1); //site

        return $doc;
    }

    public function check_access($id) {
        return manager::ACCESS_GRANTED;
	}

    public function get_doc_url(document $doc) {
        global $PAGE;
        if ($doc->is_set('url')) {
            $url = $doc->get('url');
        } else {
            $url = $PAGE->url;
        }
        return new moodle_url($url);
    }

    public function get_context_url(document $doc) {
        global $PAGE;
        if ($doc->is_set('url')) {
            $url = $doc->get('url');
        } else {
            $url = $PAGE->url;
        }
        return new moodle_url($url);
    }

    protected function get_module_name() {
        return 'local_externaldocument';
    }

}
