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

require_once(__DIR__ . '/../../config.php');

$context = context_system::instance();
$PAGE->set_context($context);

$blockannouncementsinfrontpage = !empty(\block_announcements\manager::get_announcements_blocks_in_context(2));

if (!$blockannouncementsinfrontpage) {
    require_login();
}
//require_capability('block/announcements:addinstance', $context);
$category = required_param('category', PARAM_RAW);
$newid = optional_param('newid', '', PARAM_INT);

$viewall_url = new moodle_url('/blocks/announcements/viewall.php', array('category' => $category));
$PAGE->set_url($viewall_url);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_title(get_string('pluginname', 'block_announcements'));
$PAGE->set_heading(get_string('pluginname', 'block_announcements'));

$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_announcements'));
$PAGE->navbar->add(get_string('view_link_other_announcements', 'block_announcements'), $viewall_url);

echo $OUTPUT->header();

$announcements = \block_announcements\manager::get_enabled_blocks_by_category($category, 'position', $newid);



$data = new stdClass;
$data->announcements = array();
$userrolesatsystemcontext = get_user_roles(\context_system::instance());

foreach ($announcements as $announcement) {

    if (!$announcement->is_visible_for_user_with_roles($userrolesatsystemcontext)){
        continue;
    }

    $announcement_obj = new stdClass;
    $announcement_obj->imageurl = $announcement->get_img_url();
    $announcement_obj->title = format_text($announcement->get_title(), FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    $announcement_obj->content = format_text($announcement->get_content(), FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    $announcement_obj->url = format_text($announcement->get_url(), FORMAT_PLAIN, array('trusted' => true, 'noclean' => true));
    $announcement_obj->link_target = $announcement->get_link_target();
    $announcement_obj->fontawesome_icon = format_text($announcement->get_icon(), FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    $announcement_obj->newurl = new moodle_url($PAGE->url, array('category'=>$category, 'newid'=>$announcement->get_id()));
    $data->announcements[] = $announcement_obj;
}
if (!$newid) {
    echo $OUTPUT->render_from_template('block_announcements/viewall', $data);
} else {
    echo $OUTPUT->render_from_template('block_announcements/viewone', $data);
}

echo $OUTPUT->footer();
