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
 * Document representation.
 *
 * @package    search_solr_custom
 * @copyright  2021 Joan Carbassa <joan.carbassa@ithinkupc.com>
 */

namespace search_solr_custom;

defined('MOODLE_INTERNAL') || die();

/**
 * Respresents a document to index.
 *
 * @copyright  2021 Joan Carbassa <joan.carbassa@ithinkupc.com>
 */
class document extends \search_solr\document {

    /**
     * All optional fields docs can contain.
     *
     * Although it matches solr fields format, this is just to define the field types. Search
     * engine plugins are responsible of setting their appropriate field types and map these
     * naming to whatever format they need.
     *
     * @var array
     */
    protected static $optionalfields = array(
        'userid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'groupid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'description1' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'description2' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'author' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'author_facet' => array(
            'type' => 'string',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'university' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'university_facet' => array(
            'type' => 'string',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'url' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'exttype' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'language' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'tags' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'coursecredits' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'starttime' => array(
            'type' => 'tdate',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'endtime' => array(
            'type' => 'tdate',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'languageofwork' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'givenname' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'familyname' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'employer' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'employerstarttime' => array(
            'type' => 'tdate',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'employerendtime' => array(
            'type' => 'tdate',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'occupation' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'orcidid' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
    );

}
