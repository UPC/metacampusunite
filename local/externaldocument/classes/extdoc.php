<?php


namespace local_externaldocument;

use core_search\manager;
use local_externaldocument\types\course;
use search_solr\engine;
use stdClass;
use moodle_exception;
use coding_exception;
use local_externaldocument\types\person;
use local_externaldocument\types\webpage;

abstract class extdoc {

    protected $fieldtypes = array(
        'title' => PARAM_TEXT,
        'content' => PARAM_TEXT,
        'tags' => PARAM_TEXT,
    );

    protected $tablename;

    protected $uniquefields;

    public static function instance(string $type) {
        switch ($type) {
            case 'course':
                return new course();

            case 'person':
                return new person();

            case 'webpage':
                return new webpage();

            default:
                throw new moodle_exception('error:type_not_implemented', 'local_externaldocument');
        }
    }

    public function get_field_names() {
        return array_keys($this->fieldtypes);
    }

    public function get_table_name() {
        return $this->tablename;
    }

    public function get_field_type(string $fieldtype) {
        if (isset($this->fieldtypes[$fieldtype])) {
            return $this->fieldtypes[$fieldtype];
        }

        throw new coding_exception('Type \'' . $fieldtype . '\' not found. You can add a type calling the function set_field_type().');
    }

    protected function set_field_type(string $fieldname, string $type): void {
        $this->fieldtypes[$fieldname] = $type;
    }

    protected function set_tablename(string $tablename): void {
        $this->tablename = $tablename;
    }

    public function set_uniquefields($uniquefields): void {
        $this->uniquefields = $uniquefields;
    }

    public function add(stdClass $extdoc) {
        global $DB;

        $object = $this->load($extdoc);
        if ($existingrecord = $this->get_if_exists($object)) {
            $object->id = $existingrecord->id;
            return $this->update($object);
        } else {
            return $DB->insert_record($this->tablename, $object);
        }
    }

    public function get(int $id) {
        global $DB;

        return $DB->get_record($this->tablename, array('id', $id));
    }

    public function update(stdClass $extdoc) {
        global $DB;

        return $DB->update_record($this->tablename, $extdoc);
    }

    public function delete(int $id) {
        global $DB;

        $this->delete_from_search_engine($id);
        $DB->delete_records($this->tablename, array('id' => $id));
    }

    private function delete_from_search_engine(int $id): void {
        if (manager::is_indexing_enabled()) {
            $uniquefields = $this->get_uniquefields_record($id);
            $querydata = new stdClass();
            $querydata->q = 'areaid:local_externaldocument-' . $this->get_extdoc_type();
            foreach ($uniquefields as $fieldname => $fieldvalue) {
                $querydata->q .= ' AND ' . $fieldname . ':' . $fieldvalue;
            }
            $engine = manager::instance()->get_engine();
            $results = $engine->execute_query($querydata, (object)['everything' => true]);
            if (isset($results[0])) {
                $searchid = $results[0]->get('id');
                $engine->delete_by_id($searchid);
            }
        }
    }

    public function get_by_field($field, $value) {
        global $DB;

        return $DB->get_record($this->tablename, array($field => $value));
    }

    private function get_uniquefields_record(int $id) {
        global $DB;

        $sql = "SELECT " . $this->uniquefields .
            " FROM {" . $this->tablename .
            "} WHERE id = ?";
        return $DB->get_record_sql($sql, array($id));
    }

    abstract public function load(stdClass $object): stdClass;

    abstract public function get_if_exists(stdClass $loadedobject);

    abstract public function parse_csv_dates($row);

    abstract public function get_select_query(): string;

    abstract public function get_extdoc_type(): string;

}