<?php

namespace local_externaldocument\search;

use core_search\base_mod,
    context,
    core_search\document_factory,
    core_search\manager,
    core_search\document,
    moodle_url;

defined('MOODLE_INTERNAL') || die();

class course extends base_mod {

    public function get_document_recordset($modifiedfrom = 0, context $context = null) {
        global $DB;

        $sql = "SELECT *
                FROM {extdoc_t_course}
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
        $doc->set('author', $record->author);
        $doc->set('author_facet', $record->author);
        $doc->set('university', $record->university);
        $doc->set('university_facet', $record->university);
        $doc->set('url', $record->url);
        $doc->set('exttype', $this->areaname);
        $doc->set('language', $record->language);
        $doc->set('tags', $record->tags);
        if (isset($record->coursecredits)) {
            $doc->set('coursecredits', $record->coursecredits);
        }
        if (isset($record->starttime)) {
            $doc->set('starttime', $record->starttime);
        }
        if (isset($record->endtime)) {
            $doc->set('endtime', (int) $record->endtime);
        }
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
        return new moodle_url($doc->get('url'));
    }

    public function get_context_url(document $doc) {
        return new moodle_url($doc->get('url'));
    }

    protected function get_module_name() {
        return 'local_externaldocument';
    }

}
