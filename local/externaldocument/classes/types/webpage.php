<?php

namespace local_externaldocument\types;

use local_externaldocument\extdoc,
    stdClass,
    moodle_exception;

class webpage extends extdoc {

    public function __construct() {
        $this->set_tablename('extdoc_t_webpage');
        $this->set_uniquefields('url');
        $this->set_field_type('description1', PARAM_TEXT);
        $this->set_field_type('url', PARAM_URL);
        $this->set_field_type('language', PARAM_TEXT);
        $this->set_field_type('languageofwork', PARAM_TEXT);
    }

    public function load(stdClass $object): stdClass {
        if (empty($object->url)) throw new moodle_exception('error:no_url', 'local_externaldocument');
        $webpage_object = new stdClass();
        $webpage_object->url = $object->url;
        $webpage_object->title = (!empty($object->title)) ? $object->title : null;
        $webpage_object->content = (!empty($object->content)) ? $object->content : null;
        $webpage_object->description1 = (!empty($object->description1)) ? $object->description1 : null;
        $webpage_object->description2 = (!empty($object->description2)) ? $object->description2 : null;
        $webpage_object->language = (!empty($object->language)) ? $object->language : null;
        $webpage_object->languageofwork = (!empty($object->languageofwork)) ? $object->languageofwork : null;
        $webpage_object->tags = (!empty($object->tags)) ? $object->tags : null;
        $webpage_object->timemodified = time();
        return $webpage_object;
    }

    public function parse_csv_dates($row){}

    public function get_if_exists(stdClass $loadedobject) {
        global $DB;

        return $DB->get_record_select($this->tablename, 'url = :url', array('url' => $loadedobject->url));
    }

    public function get_select_query(): string {
        return "id, title, content, tags, description1, url, language, languageofwork";
    }

    public function get_extdoc_type(): string {
        return 'webpage';
    }

}