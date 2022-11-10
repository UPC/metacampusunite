<?php
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
    $url = new \moodle_url('/blocks/carousel/manager/edit.php', array('id' => $id));
} else {
    $url = new \moodle_url('/blocks/carousel/manager/edit.php', array('filter_category' => $filter_category));
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);

require_login($course);
require_capability('block/carousel:addinstance', $context);

// Init object carousel
$carousel = new \block_carousel\carousel();
$carousel->get($id);
// End init object carousel

// Set page variables and urls
$PAGE->set_title(get_string('carousels_manage', 'block_carousel'));
$PAGE->set_heading(get_string('carousels_manage', 'block_carousel'));
if ($id > 0) {
    $urlindex = new \moodle_url('/blocks/carousel/manager', array('filter_category' => $carousel->get_category()));
} else {
    $urlindex = new \moodle_url('/blocks/carousel/manager', array('filter_category' => $filter_category));
}
$PAGE->navbar->add(get_string('carousels_manage', 'block_carousel'), $urlindex);
if ($id > 0) {
    $PAGE->navbar->add(get_string('edit'), $url);
} else {
    $PAGE->navbar->add(get_string('new'), $url);
}
$PAGE->add_body_class('block_carousel');
// End set page variables and urls

// Form carousel
$carousel_form = new \block_carousel\carousel_form($url, null, 'post', '', array('id' => 'carousel_form'));

if ($carousel_form->is_cancelled()) { //Handle form cancel operation, if cancel button is present on form
    redirect($urlindex);
}

if ($data = $carousel_form->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.

    // Del img?
    $img = $data->img;
    if (isset($data->deletepicture) && $data->deletepicture == 1) {
        $carousel->delete_img();
        $img = null;
    }

    $carousel->set_title($data->title);
    $carousel->set_content($data->content);
    $carousel->set_alt($data->alt);
    $carousel->set_url($data->url);
    $carousel->set_img($img);
    if (isset($data->enabled)) {
        $carousel->set_enabled($data->enabled);
    } else {
        $carousel->set_enabled(0);
    }
    $carousel->set_link_target($data->link_target);

    // Prioritzem: si intenten crear una nova categoria aquesta és la que val
    // Si han seleccionat una, aquella és la que val
    // sino, doncs no volem categoria
    if (isset($data->category) && !empty($data->category)) {
        $carousel->set_category($data->category);
    } else if (isset($data->filter_category) && !empty($data->filter_category)) {
        $carousel->set_category($data->filter_category);
    } else {
        $carousel->set_category();
    }

    $carousel->set_roles($data->roles);
    $result = $carousel->save();

    if ($result) {
        // Image data
        if ($data->img) {
            if ($filename = $carousel_form->get_new_filename('img')) {
                $carousel->save_img($data->img);
            }
        }

        $urlredirect = new moodle_url('/blocks/carousel/manager', array('filter_category' => $carousel->get_category()));
        redirect($urlredirect, get_string('carousel_save_ok', 'block_carousel'));
    }

    redirect($url, get_string('carousel_save_ko', 'block_carousel'));
}

// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
// or on the first display of the form.
$form_data = array();
$form_data['id'] = $carousel->get_id();
$form_data['title'] = $carousel->get_title();
$form_data['icon'] = $carousel->get_icon();
$form_data['content'] = $carousel->get_content();
$form_data['alt'] = $carousel->get_alt();
if ($id > 0) {
    $form_data['filter_category'] = $carousel->get_category();
}  else {
    $form_data['filter_category'] = $filter_category;
}
$form_data['url'] = $carousel->get_url();
$form_data['enabled'] = $carousel->get_enabled();
$form_data['link_target'] = $carousel->get_link_target();
$form_data['img'] = $carousel->get_img();

$img_url = $carousel->get_img_url();
$form_data['currentpicture'] = get_string('carousel_no_img', 'block_carousel');
if ($img_url != null) {
    if($img_url->out() != '') {
        $form_data['currentpicture'] = html_writer::empty_tag('img', array('src' => $img_url->out(), 'width' => 100, 'height' => 100));
    }
}

$form_data['roles'] = $carousel->get_roles();
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('carousels_manage', 'block_carousel'));
$carousel_form->set_data($form_data);
$carousel_form->display();
echo $OUTPUT->footer();