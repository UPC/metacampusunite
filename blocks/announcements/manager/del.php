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

require_once('../../../config.php');

$id = required_param('id', PARAM_INTEGER);
$confirmed = optional_param('confirmed', 0, PARAM_INTEGER);
$contextid = context_system::instance()->id;
list($context, $course, $cm) = get_context_info_array($contextid);

$urlindex = null;
$url = new moodle_url('/blocks/announcements/manager/del.php', array('id' => $id));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);

require_login($course);
require_capability('block/announcements:addinstance', $context);

$announcement = new \block_announcements\announcement();
$announcement->get($id);
$urlindex = new moodle_url('/blocks/announcements/manager', array('filter_category' => $announcement->get_category()));

$PAGE->navbar->add(get_string('announcements_manage', 'block_announcements'), $urlindex);
$PAGE->navbar->add(get_string('delete'), $url);

if ($announcement->get_id() > 0) {
    if ($confirmed == 1) {
        $result = $announcement->delete();
        if ($result) {
            redirect($urlindex, get_string('announcement_save_ok', 'block_announcements'));
        } else {
            redirect($urlindex, get_string('announcement_save_ko', 'block_announcements'));
        }
    }

    $confirmurl = new moodle_url('/blocks/announcements/manager/del.php', array('id' => $id, 'confirmed' => 1));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('announcements_manage', 'block_announcements'));
    echo $OUTPUT->confirm(get_string('announcement_del_confirm', 'block_announcements', $announcement->get_title()), $confirmurl, $urlindex);
    echo $OUTPUT->footer();
    die();
}
redirect($urlindex);
