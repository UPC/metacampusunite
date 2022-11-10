<?php
// ------------------------
// @FUNC F030 AT: Block announcements.
// Block que mostra noticies.
// ---Fi
//
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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Ajax to go down announcement 1 position.
 */
require_once('../../../../config.php');

// Instantiate context
$context = context_system::instance();
$PAGE->set_context($context);

require_login(); // We need login
require_capability('block/announcements:addinstance', $context);

// Config vars
$query = '';
$id = required_param('id', PARAM_INT); // Get Id to Search
// End config vars

$announcement = new \block_announcements\announcement();
if ($announcement->get($id)) {
    \block_announcements\manager::move_block($id, 1);
}

$item = new stdClass();
$item->result = 'ok';

// Print response
header('Expires: 0');
header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Content-Type: application/json; charset=utf-8');
echo json_encode($item);