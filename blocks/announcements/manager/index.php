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
/*
require_once('classes/list_frontpage_blocks_categorys.class.php');
require_once('classes/frontpage_block_categorys_manager.class.php');
require_once('forms/user_category_filter_form.php');*/

// Init user category
$filter_category = optional_param('filter_category', '', PARAM_ALPHANUM);
$all_categorys = \block_announcements\manager::get_all_categorys();
if (!in_array($filter_category, $all_categorys)) {
    $filter_category = '';
}
// End user category

$contextid = context_system::instance()->id;

list($context, $course, $cm) = get_context_info_array($contextid);

$base_url = new moodle_url('/blocks/announcements/manager', array());
$url = new moodle_url('/blocks/announcements/manager', array('filter_category' => $filter_category));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);

require_login($course);
require_capability('block/announcements:addinstance', $context);

$PAGE->set_title(get_string('announcements_manage', 'block_announcements'));
$PAGE->set_heading(get_string('announcements_manage', 'block_announcements'));
$PAGE->navbar->add(get_string('announcements_manage', 'block_announcements'), $url);
$PAGE->add_body_class('block_announcements');

// Include js
$PAGE->requires->js_call_amd('block_announcements/manager', 'init');
// End include js

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('announcements_manage', 'block_announcements'));

// Form select category
$user_category_filter_form = new \block_announcements\category_filter_form($base_url, null, 'get', '', array('id' => 'category_filter_form'));
$form_data = array();
$form_data['filter_category'] = $filter_category;
$user_category_filter_form->set_data($form_data);
$user_category_filter_form->display();

// Link to create block category
$newblockcategory = new moodle_url('/blocks/announcements/manager/edit.php', array('filter_category' => $filter_category));
echo html_writer::nonempty_tag(
    'a',
    get_string('new'),
    array(
        'id' => 'link_new',
        'class' => 'btn btn-secondary',
        'href' => $newblockcategory->out()
    )
);

$table = new \block_announcements\list_announcements('list_announcements', $filter_category);
$items_by_page = 10;
$table->define_baseurl($url->out());
$table->out($items_by_page, true);

echo $OUTPUT->footer();