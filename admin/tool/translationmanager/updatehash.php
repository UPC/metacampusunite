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
 * Translation manager
 *
 * @package    tool
 * @subpackage translationmanager
 * @copyright  2020 Farhan Karmali <farhan6318@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_translationmanager\form\updatehash_form;
require('../../../config.php');

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/formslib.php');
$PAGE->set_context(context_system::instance());
require_login();
require_capability('filter/fulltranslate:edittranslations', context_system::instance());
$url = new moodle_url('/admin/tool/edit.php');
$PAGE->set_url($url);
$PAGE->set_title('Full translate filter');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_translationmanager'));

$mform = new updatehash_form();
if ($fromformdata = $mform->get_data()) {
    //die(print_object($fromformdata));

    $oldhashkey = $fromformdata->oldhash;
    $newhashkey = $fromformdata->newhash;
    $DB->set_debug(true);
    $DB->set_field('filter_fulltranslate', 'hashkey', $newhashkey, ['hashkey' => $oldhashkey]);
    $DB->set_debug(false);
    echo "updated";

} else {
    $mform->display();
}


echo $OUTPUT->footer();