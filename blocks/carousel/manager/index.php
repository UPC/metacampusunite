<?php
// ------------------------
// @FUNC F030 AT: Block carousel.
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
require_once('../lib.php');
/*
require_once('classes/list_frontpage_blocks_categories.class.php');
require_once('classes/frontpage_block_categories_manager.class.php');
require_once('forms/user_category_filter_form.php');*/

// Init user category
$filter_category = optional_param('filter_category', '', PARAM_ALPHANUM);
$all_categories = \block_carousel\manager::get_all_categories();

if (!in_array($filter_category, $all_categories)) {
    $filter_category = '';
}
// End user category

$contextid = context_system::instance()->id;

list($context, $course, $cm) = get_context_info_array($contextid);

$base_url = new moodle_url('/blocks/carousel/manager', array());
$url = new moodle_url('/blocks/carousel/manager', array('filter_category' => $filter_category));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);


require_login($course);
require_capability('block/carousel:addinstance', $context);

$PAGE->set_title(get_string('carousels_manage', 'block_carousel'));
$PAGE->set_heading(get_string('carousels_manage', 'block_carousel'));
$PAGE->navbar->add(get_string('carousels_manage', 'block_carousel'), $url);
$PAGE->add_body_class('block_carousel');

// Include js
$PAGE->requires->js_call_amd('block_carousel/manager', 'init'); //#TODO# is it necessary
// End include js

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('carousels_manage', 'block_carousel'));

// Form select category
echo block_carousel_get_category_sigle_select($filter_category);

// Link to create block category
$newblockcategory = new moodle_url('/blocks/carousel/manager/edit.php', array('filter_category' => $filter_category));
echo html_writer::nonempty_tag(
    'a',
    get_string('new'),
    array(
        'id' => 'link_new',
        'class' => 'btn btn-secondary',
        'href' => $newblockcategory->out()
    )
);

$data = [];
$data['images'] = array_values(block_carousel_get_images($filter_category));


echo $OUTPUT->render_from_template('block_carousel/carousel_manager_list', $data);

echo $OUTPUT->footer();