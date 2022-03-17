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
 * Solr custom engine.
 *
 * @package    search_solr_custom
 * @copyright  2021 Joan Carbassa <joan.carbassa@ithinkupc.com>
 */

namespace search_solr_custom;

defined('MOODLE_INTERNAL') || die();

/**
 * Solr engine.
 *
 * @package    search_solr_custom
 * @copyright  2021 Joan Carbassa <joan.carbassa@ithinkupc.com>
 */
class engine extends \search_solr\engine {

    const MAX_DYNAMIC_SEARCH_RESULTS = 5;

    /**
     * @var array Fields that can be highlighted.
     */
    protected $highlightfields = array('title', 'content', 'description1', 'description2');


    /**
     * Gets the document class used by this search engine.
     *
     * Search engines can overwrite \core_search\document with \search_ENGINENAME\document class.
     *
     * Looks for a document class in the current search engine namespace, falling back to \core_search\document.

     * Publicly available because search areas do not have access to the engine details,
     * \core_search\document_factory accesses this function.
     *
     * @return string
     */
    public function get_document_classname() {
        $classname = $this->pluginname . '\\document';
        if (!class_exists($classname)) {
            $classname = '\\search_solr_custom\\document';
        }
        return $classname;
    }


    /**
     * Returns a document instance prepared to be rendered.
     *
     * @param \core_search\base $searcharea
     * @param array $docdata
     * @return \core_search\document
     */
    protected function to_document(\core_search\base $searcharea, $docdata) {
		global $OUTPUT;

		$docdata['description2'] = $OUTPUT->render_from_template('search_solr_custom/description2_custom', $docdata);
		return parent::to_document($searcharea, $docdata);
	}

    public function is_server_ready() {

        $configured = $this->is_server_configured();
        if ($configured !== true) {
            return $configured;
        }

        // As part of the above we have already checked that we can contact the server. For pages
        // where performance is important, we skip doing a full schema check as well.
        if ($this->should_skip_schema_check()) {
            return true;
        }

        // Update schema if required/possible.
        $schemalatest = $this->check_latest_schema();
        if ($schemalatest !== true) {
            return $schemalatest;
        }

        // Check that the schema is already set up.
        try {
            $schema = new schema($this);
            $schema->validate_setup();
        } catch (\moodle_exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function execute_query($filters, $accessinfo, $limit = 0) {
        global $USER;

        if (empty($limit)) {
            $limit = \core_search\manager::MAX_RESULTS;
        }

        // If there is any problem we trigger the exception as soon as possible.
        $client = $this->get_search_client();

        // Create the query object.
        $query = $this->create_user_query($filters, $accessinfo);
        if ($filters->facetfields) {
            $query->setFacet(true);
            foreach ($filters->facetfields as $facetfield) {
                $query->addFacetField($facetfield);
            }
        }

        // If the query cannot have results, return none.
        if (!$query) {
            return [];
        }

        // We expect good match rates, so for our first get, we will get a small number of records.
        // This significantly speeds solr response time for first few pages.
        $query->setRows(min($limit * 3, static::QUERY_SIZE));
        $response = $this->get_query_response($query);

        // Get count data out of the response, and reset our counters.
        list($included, $found) = $this->get_response_counts($response);
        $this->totalenginedocs = $found;
        $this->processeddocs = 0;
        $this->skippeddocs = 0;
        if ($included == 0 || $this->totalenginedocs == 0) {
            // No results.
            return array();
        }

        // Get valid documents out of the response.
        if (!$query->getFacet()) {
            $results = $this->process_response($response, $limit);

            // We have processed all the docs in the response at this point.
            $this->processeddocs += $included;

            // If we haven't reached the limit, and there are more docs left in Solr, lets keep trying.
            while (count($results) < $limit && ($this->totalenginedocs - $this->processeddocs) > 0) {
                // Offset the start of the query, and since we are making another call, get more per call.
                $query->setStart($this->processeddocs);
                $query->setRows(static::QUERY_SIZE);

                $response = $this->get_query_response($query);
                list($included, $found) = $this->get_response_counts($response);
                if ($included == 0 || $found == 0) {
                    // No new results were found. Found being empty would be weird, so we will just return.
                    return $results;
                }
                $this->totalenginedocs = $found;

                // Get the new response docs, limiting to remaining we need, then add it to the end of the results array.
                $newdocs = $this->process_response($response, $limit - count($results));
                $results = array_merge($results, $newdocs);

                // Add to our processed docs count.
                $this->processeddocs += $included;
            }
        } else {
            $results = $this->get_facet_results($response);
        }

        return $results;
    }

    private function get_facet_results($response): array {
        $facetresults = array();
        foreach ($response->facet_counts->facet_fields as $facetfield => $facetcontent) {
            $maxresults = 0;
            foreach ($facetcontent as $facetname => $facetcount) {
                if (!$facetcount) continue;
                if ($maxresults == self::MAX_DYNAMIC_SEARCH_RESULTS) {
                    $maxresults = 0;
                    break;
                }
                $facetresults[$facetfield][$facetname] = $facetcount;
                $maxresults++;
            }
        }

        return $facetresults;
    }

}
