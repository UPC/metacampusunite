<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_externaldocument
 * @category    string
 * @copyright   2021 Joan Carbassa <joan.carbassa@ithinkupc.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'External document';
$string['manage'] = 'Configure';
$string['showinnavigation'] = 'Text';
$string['showinnavigation_desc'] = 'Description';


// Search
$string['search:course'] = 'External Document - Course';
$string['search:webpage'] = 'External Document - Webpage';
$string['search:person'] = 'External Document - Person';

//Web Services
$string['error:no_json'] = 'The format must be a JSON with at least one object with title and URL';
$string['error:no_title_or_url'] = 'The object must contain at least a title and an URL';
$string['error:type_not_implemented'] = 'This type of document has not been implemented yet';
$string['error:max_documents_exceeded'] = 'Maximum documents per call exceeded';
$string['maxdocumentsperwscall'] = 'Max documents per webservice call';
$string['maxdocumentsperwscall_desc'] = 'Max documents allowed on a single JSON on a local_externaldocument upload webservice call';
$string['ws_param_not_found_in_document'] = '{$a->param} not found on the document';
$string['ws_param_too_long'] = '{$a->param} is over the maximum characters allowed';

//Extdoc creation page
$string['create_page_title'] = 'Add extdoc';

//Extdoc list page
$string['list_page_title'] = 'External documents list';