<?php

namespace local_externaldocument\types;

use local_externaldocument\extdoc,
    stdClass,
    moodle_exception;

class person extends extdoc {

    public function __construct() {
        $this->set_tablename('extdoc_t_person');
        $this->set_uniquefields('givenname, familyname');
        $this->set_field_type('givenname', PARAM_TEXT);
        $this->set_field_type('familyname', PARAM_TEXT);
        $this->set_field_type('employer', PARAM_TEXT);
        $this->set_field_type('employerstarttime', PARAM_TIMEZONE);
        $this->set_field_type('employerendtime', PARAM_TIMEZONE);
        $this->set_field_type('url', PARAM_URL);
        $this->set_field_type('occupation', PARAM_TEXT);
        $this->set_field_type('orcidid', PARAM_TEXT);
    }

    public function load(stdClass $object): stdClass {
        if (empty($object->givenname) || empty($object->familyname)) throw new moodle_exception('error:no_givenname', 'local_externaldocument');
        $person_object = new stdClass();
        $person_object->givenname = $object->givenname;
        $person_object->familyname = $object->familyname;
        $person_object->title = $object->givenname . ' ' . $object->familyname;
        $person_object->employer = (!empty($object->employer)) ? $object->employer : null;
        $person_object->employerstarttime = (!empty($object->employerstarttime)) ? $object->employerstarttime : null;
        $person_object->employerendtime = (!empty($object->employerendtime)) ? $object->employerendtime : null;
        $person_object->tags = (!empty($object->tags)) ? $object->tags : null;
        $person_object->url = (!empty($object->url)) ? $object->url : null;
        $person_object->occupation = (!empty($object->occupation)) ? $object->occupation : null;
        $person_object->orcidid = (!empty($object->orcidid)) ? $object->orcidid : null;
        $person_object->timemodified = time();
        return $person_object;
    }

    public function parse_csv_dates($row) {
        $row->employerstarttime = strtotime(str_replace('/', '-', $row->starttime));
        $row->employerendtime = strtotime(str_replace('/', '-', $row->employerendtime));
    }


    public function get_if_exists(stdClass $loadedobject) {
        global $DB;

        return $DB->get_record_select($this->tablename, 'givenname = :givenname AND familyname = :familyname',
            array('givenname' => $loadedobject->givenname, 'familyname' => $loadedobject->familyname)
        );
    }

    public function get_select_query(): string {
        return "id, title, content, tags, givenname, familyname, employer, to_timestamp(employerstarttime)::date || 'T' || to_timestamp(employerstarttime)::time as employerstarttime, to_timestamp(employerendtime)::date || 'T' || to_timestamp(employerendtime)::time as employerendtime, url, occupation, orcidid";
    }

    public function get_extdoc_type(): string {
        return 'person';
    }

}