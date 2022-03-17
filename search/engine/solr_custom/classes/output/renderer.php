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
 * Search renderer.
 *
 * @package    search_solr_custom
 * @copyright  2021 Joan Carbassa <joan.carbassa@ithinkupc.com>
 */

namespace search_solr_custom\output;

use core_search\manager;
use stdClass;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Search renderer.
 *
 * @package    search_solr_custom
 * @copyright  2021 Joan Carbassa <joan.carbassa@ithinkupc.com>
 */
class renderer extends \core_search\output\renderer {

    public static function extend_global_search_form($mform, $query) {
        global $PAGE, $OUTPUT;

        $facetfields = self::get_facet_fields($query);
        foreach ($facetfields as $facetfieldname => $facetfield) {
            $mform->addElement('header', $facetfieldname, get_string($facetfieldname, 'search_solr_custom'));
            $mform->setExpanded($facetfieldname, false);
            foreach ($facetfield as $facetname => $facetvalue) {
                $data = new stdClass();
                $data->fieldname = $facetfieldname;
                $data->fieldvalue = $facetname;
                $data->fieldcount = $facetvalue;
                $data->fieldclass = self::get_field_class($query, $facetfieldname, $facetname);
                if ($data->fieldclass == 'removefieldfilter') $data->removefield = true;
                $filterablefield = $OUTPUT->render_from_template('search_solr_custom/filterablefield', $data);
                $mform->addElement('static', $facetname, $filterablefield);
            }
        }
        $PAGE->requires->js_call_amd('search_solr_custom/customquery', 'init');
    }

    private static function get_facet_fields($query) {
        $searchdata = new stdClass();
        $searchdata->q = $query ?: ' ';
        $searchdata->facetfields = array('author_facet', 'university_facet');
        $search = manager::instance(true, true);
        return $search->search($searchdata);
    }

    private static function get_field_class($query, $fieldname, $fieldvalue) {
        $querytosearch = $fieldname . ':"' . $fieldvalue . '"';
        if (strpos($query, $querytosearch) !== false ) {
            return 'removefieldfilter';
        } else {
            return 'addfieldfilter';
        }
    }

    /**
     * Displaying search results.
     *
     * @param \core_search\document Containing a single search response to be displayed.a
     * @return string HTML
     */
    public function render_result(\core_search\document $doc) {
        $docdata = $doc->export_for_template($this);

        // Limit text fields size.
        $docdata['title'] = shorten_text($docdata['title'], static::SEARCH_RESULT_STRING_SIZE, true);
        $docdata['content'] = $docdata['content'] ? shorten_text($docdata['content'], static::SEARCH_RESULT_TEXT_SIZE, true) : '';
        $docdata['description1'] = $docdata['description1'] ? shorten_text($docdata['description1'], static::SEARCH_RESULT_TEXT_SIZE, true) : '';
        // $docdata['description2'] = $docdata['description2'] ? shorten_text($docdata['description2'], static::SEARCH_RESULT_TEXT_SIZE, true) : '';
		// Prevent shorten_text and html_entity_encode
        $docdata['description2'] = html_entity_decode($docdata['description2']);

        return $this->output->render_from_template('core_search/result', $docdata);
    }

}
