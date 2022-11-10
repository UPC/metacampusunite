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

require_once('../../../config.php');;

$id = optional_param('id', 0, PARAM_INTEGER);
$filter_category = optional_param('filter_category', '', PARAM_ALPHANUM);
$contextid = context_system::instance()->id;
list($context, $course, $cm) = get_context_info_array($contextid);

$urlindex = null;
if ($id > 0) {
    $url = new \moodle_url('/blocks/announcements/manager/edit.php', array('id' => $id));
} else {
    $url = new \moodle_url('/blocks/announcements/manager/edit.php', array('filter_category' => $filter_category));
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);

require_login($course);
require_capability('block/announcements:addinstance', $context);

// Init object announcement
$announcement = new \block_announcements\announcement();
$announcement->get($id);
// End init object announcement

// Set page variables and urls
$PAGE->set_title(get_string('announcements_manage', 'block_announcements'));
$PAGE->set_heading(get_string('announcements_manage', 'block_announcements'));
if ($id > 0) {
    $urlindex = new \moodle_url('/blocks/announcements/manager', array('filter_category' => $announcement->get_category()));
} else {
    $urlindex = new \moodle_url('/blocks/announcements/manager', array('filter_category' => $filter_category));
}
$PAGE->navbar->add(get_string('announcements_manage', 'block_announcements'), $urlindex);
if ($id > 0) {
    $PAGE->navbar->add(get_string('edit'), $url);
} else {
    $PAGE->navbar->add(get_string('new'), $url);
}
$PAGE->add_body_class('block_announcements');
// End set page variables and urls

// Form announcement
$announcement_form = new \block_announcements\announcement_form($url, null, 'post', '', array('id' => 'announcement_form'));

if ($announcement_form->is_cancelled()) { //Handle form cancel operation, if cancel button is present on form
    redirect($urlindex);
}

if ($data = $announcement_form->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.

    // Del img?
    if (isset($data->deletepicture) && $data->deletepicture == 1) {
        $announcement->delete_img();
    }

    $announcement->set_title($data->title);
    $announcement->set_icon($data->icon);
    $announcement->set_content($data->content['text']);
    $announcement->set_url($data->url);
    if (isset($data->enabled)) {
        $announcement->set_enabled($data->enabled);
    } else {
        $announcement->set_enabled(0);
    }
    $announcement->set_link_target($data->link_target);

    // Prioritzem: si intenten crear una nova categoria aquesta és la que val
    // Si han seleccionat una, aquella és la que val
    // sino, doncs no volem categoria
    if (isset($data->category) && !empty($data->category)) {
        $announcement->set_category($data->category);
    } else if (isset($data->filter_category) && !empty($data->filter_category)) {
        $announcement->set_category($data->filter_category);
    } else {
        $announcement->set_category();
    }

    $announcement->set_roles($data->roles);
    $announcement->set_alt($data->alt);
    $result = $announcement->save();

    if ($result) {
        // Image data
        if ($data->img) {
            if ($filename = $announcement_form->get_new_filename('img')) {
                $announcement->save_img($data->img);
            }
        }

        $urlredirect = new moodle_url('/blocks/announcements/manager', array('filter_category' => $announcement->get_category()));
        redirect($urlredirect, get_string('announcement_save_ok', 'block_announcements'));
    }

    redirect($url, get_string('announcement_save_ko', 'block_announcements'));
}

// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
// or on the first display of the form.
$form_data = array();
$form_data['id'] = $announcement->get_id();
$form_data['title'] = $announcement->get_title();
$form_data['icon'] = $announcement->get_icon();
$form_data['content'] = array('text' => $announcement->get_content(), 'format' => 1);
if ($id > 0) {
    $form_data['filter_category'] = $announcement->get_category();
}  else {
    $form_data['filter_category'] = $filter_category;
}
$form_data['url'] = $announcement->get_url();
$form_data['enabled'] = $announcement->get_enabled();
$form_data['link_target'] = $announcement->get_link_target();
$img_url = $announcement->get_img_url();
$form_data['currentpicture'] = get_string('announcement_no_img', 'block_announcements');
if ($img_url != null) {
    if($img_url->out() != '') {
        $form_data['currentpicture'] = html_writer::empty_tag('img', array('src' => $img_url->out(), 'width' => 100, 'height' => 100));
    }
}
$form_data['roles'] = $announcement->get_roles();
$form_data['alt'] = $announcement->get_alt();
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('announcements_manage', 'block_announcements'));
$announcement_form->set_data($form_data);
$announcement_form->display();
echo $OUTPUT->footer();