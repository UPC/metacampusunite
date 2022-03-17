<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Solr schema manipulation manager.
 *
 * @package   search_solr
 * @copyright 2015 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace search_solr_custom;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/filelib.php');

/**
 * Schema class to interact with Solr schema.
 *
 * At the moment it only implements create which should be enough for a basic
 * moodle configuration in Solr.
 *
 * @package   search_solr
 * @copyright 2015 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schema extends \search_solr\schema {

    protected function validate_fields(&$fields, $requireexisting = false) {
        global $CFG;

        foreach ($fields as $fieldname => $data) {
            $url = $this->engine->get_connection_url('/schema/fields/' . $fieldname);
            $results = $this->curl->get($url);

            if ($this->curl->error) {
                throw new \moodle_exception('errorcreatingschema', 'search_solr', '', $this->curl->error);
            }

            if (!$results) {
                throw new \moodle_exception('errorcreatingschema', 'search_solr', '', get_string('nodatafromserver', 'search_solr'));
            }
            $results = json_decode($results);

            if ($requireexisting && !empty($results->error) && $results->error->code === 404) {
                $a = new \stdClass();
                $a->fieldname = $fieldname;
                $a->setupurl = $CFG->wwwroot . '/search/engine/solr_custom/setup_schema.php';
                throw new \moodle_exception('errorvalidatingschema', 'search_solr', '', $a);
            }

            // The field should not exist so we only accept 404 errors.
            if (empty($results->error) || (!empty($results->error) && $results->error->code !== 404)) {
                if (!empty($results->error)) {
                    throw new \moodle_exception('errorcreatingschema', 'search_solr', '', $results->error->msg);
                } else {
                    // All these field attributes are set when fields are added through this script and should
                    // be returned and match the defined field's values.

                    $expectedsolrfield = $this->doc_field_to_solr_field($data['type']);
                    if (empty($results->field) || !isset($results->field->type) ||
                        !isset($results->field->multiValued) || !isset($results->field->indexed) ||
                        !isset($results->field->stored)) {

                        throw new \moodle_exception('errorcreatingschema', 'search_solr', '',
                            get_string('schemafieldautocreated', 'search_solr', $fieldname));

                    } else if ($results->field->type !== $expectedsolrfield ||
                        $results->field->multiValued !== false ||
                        $results->field->indexed !== $data['indexed'] ||
                        $results->field->stored !== $data['stored']) {

                        throw new \moodle_exception('errorcreatingschema', 'search_solr', '',
                            get_string('schemafieldautocreated', 'search_solr', $fieldname));
                    } else {
                        // The field already exists and it is properly defined, no need to create it.
                        unset($fields[$fieldname]);
                    }
                }
            }
        }
    }

    /**
     * Setup solr stuff required by moodle.
     *
     * @param  bool $checkexisting Whether to check if the fields already exist or not
     * @return bool
     */
    public function setup($checkexisting = true) {
        $fields = document::get_default_fields_definition();

        // Field id is already there.
        unset($fields['id']);

        $this->check_index();

        $return = $this->add_fields($fields, $checkexisting);

        // Tell the engine we are now using the latest schema version.
        $this->engine->record_applied_schema_version(document::SCHEMA_VERSION);

        return $return;
    }

    /**
     * Checks the schema is properly set up.
     *
     * @throws \moodle_exception
     * @return void
     */
    public function validate_setup() {
        $fields = document::get_default_fields_definition();

        // Field id is already there.
        unset($fields['id']);

        $this->check_index();
        $this->validate_fields($fields, true);
    }

    /**
     * Returns the solr field type from the document field type string.
     *
     * @param string $datatype
     * @return string
     */
    private function doc_field_to_solr_field($datatype) {
        $type = $datatype;

        $solrversion = $this->engine->get_solr_major_version();

        switch($datatype) {
            case 'text':
                $type = 'text_general';
                break;
            case 'int':
                if ($solrversion >= 7) {
                    $type = 'pint';
                }
                break;
            case 'tdate':
                if ($solrversion >= 7) {
                    $type = 'pdate';
                }
                break;
        }

        return $type;
    }

}
