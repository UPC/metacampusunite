<?php

namespace local_externaldocument\types;

use local_externaldocument\extdoc,
    stdClass,
    moodle_exception;

class course extends extdoc {

    public function __construct() {
        $this->set_tablename('extdoc_t_course');
        $this->set_uniquefields('url');
        $this->set_field_type('author', PARAM_TEXT);
        $this->set_field_type('university', PARAM_TEXT);
        $this->set_field_type('url', PARAM_URL);
        $this->set_field_type('language', PARAM_TEXT);
        $this->set_field_type('coursecredits', PARAM_INT);
        $this->set_field_type('starttime', PARAM_TIMEZONE);
        $this->set_field_type('endtime', PARAM_TIMEZONE);
    }

    public function load(stdClass $object): stdClass {
        if (empty($object->title) || empty($object->url)) throw new moodle_exception('error:no_title_or_url', 'local_externaldocument');
        $course_object = new stdClass();
        $course_object->title = $object->title;
        $course_object->url = $object->url;
        $course_object->content = (!empty($object->content)) ? $object->content : null;
        $course_object->description1 = (!empty($object->description1)) ? $object->description1 : null;
        $course_object->description2 = (!empty($object->description2)) ? $object->description2 : null;
        $course_object->author = (!empty($object->author)) ? $object->author : null;
        $course_object->university = (!empty($object->university)) ? $object->university : null;
        $course_object->language = (!empty($object->language)) ? $object->language : null;
        $course_object->tags = (!empty($object->tags)) ? $object->tags : null;
        $course_object->coursecredits = (!empty($object->coursecredits)) ? $object->coursecredits : null;
        $course_object->starttime = (!empty($object->starttime)) ? $object->starttime : null;
        $course_object->endtime = (!empty($object->endtime)) ? $object->endtime : null;
        $course_object->timemodified = time();
        return $course_object;
    }

    public function parse_csv_dates($row) {
        $row->starttime = strtotime(str_replace('/', '-', $row->starttime));
        $row->endtime = strtotime(str_replace('/', '-', $row->endtime));
    }

    public function get_if_exists(stdClass $loadedobject) {
        global $DB;

        return $DB->get_record_select($this->tablename, 'url = :url', array('url' => $loadedobject->url));
    }

    public function get_select_query(): string {
        return "id, title, content, tags, author, university, url, language, coursecredits, to_timestamp(starttime)::date || 'T' || to_timestamp(starttime)::time as starttime, to_timestamp(endtime)::date || 'T' || to_timestamp(endtime)::time as endtime";
    }

    public function get_extdoc_type(): string {
        return 'course';
    }

}