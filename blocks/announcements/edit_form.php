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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * Form for editing announcements block settings
 *
 * @package    block_announcements
 * @copyright  2017 UPCnet
 * @author     Ferran Recio <ferran.recio@upcnet.es>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_announcements_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG;

        // Categories
        $categories = \block_announcements\manager::get_all_categorys();
        sort($categories);
        $options = array();
        $options[''] = get_string('no_category', 'block_announcements');
        foreach ($categories as $category) {
            if ($category == '') continue;
            $options[$category] = $category;
        }
        $mform->addElement('select', 'config_category', get_string('category'), $options);

        // View link other announcements
        $mform->addElement('selectyesno', 'config_view_link_other_announcements', get_string('view_link_other_announcements', 'block_announcements'));
        $mform->setDefault('config_view_link_other_announcements', 0);

        //max new on page
        $mform->addElement('text', 'config_maxelements', get_string('maxelements', 'block_announcements'));
        $mform->setDefault('config_maxelements', 3);
        $mform->setType('config_maxelements', PARAM_INT);

        $layout = array();
        $layout[''] = get_string('announcement_box_layout_select','block_announcements');
        $layouts = [get_string('announcement_box_vertical','block_announcements')=>'1', get_string('announcement_box_horizontal','block_announcements')=>'2'];
        foreach ($layouts as $k => $v) {
            if ($k == '') continue;
            $layout[$v] = $k;
        }
        $mform->addElement('select', 'config_layout', get_string('announcement_box_layout','block_announcements'), $layout);
        $mform->setDefault('config_layout', 1);

        $typebox = array();
        $typebox[''] = get_string('announcement_text_position_select','block_announcements');
        $tipos = [get_string('announcement_text_top','block_announcements')=>'1', get_string('announcement_text_bottom','block_announcements')=>'2'];
        foreach ($tipos as $k => $v) {
            if ($k == '') continue;
            $typebox[$v] = $k;
        }
        $mform->addElement('select', 'config_position', get_string('announcement_text_position','block_announcements'), $typebox);
        $mform->setDefault('config_position', 2);

        $mform->addElement('text', 'config_classes', get_string('configclasses', 'block_html'));
        $mform->setType('config_classes', PARAM_TEXT);

    }
}








