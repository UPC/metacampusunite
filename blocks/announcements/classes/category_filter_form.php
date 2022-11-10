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

namespace block_announcements;
require_once($CFG->libdir.'/formslib.php');

/**
 * Class category_filter_form
 *
 * @copyright 2017 UPCNet
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_filter_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform =& $this->_form;

        $mform->disable_form_change_checker();

        $all_categorys = \block_announcements\manager::get_all_categorys();
        $choices = array();

        $choices[''] = get_string('no_category', 'block_announcements');
        foreach ($all_categorys as $category) {
            if ($category == '') continue;
            $choices[$category] = $category;
        }
        $select = $mform->addElement('select', 'filter_category', get_string('announcement_category', 'block_announcements'), $choices, array('id' => 'filter_category'));
    }
}