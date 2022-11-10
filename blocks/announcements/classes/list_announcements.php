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

namespace block_announcements;

require_once("$CFG->libdir/tablelib.php");

class list_announcements extends \table_sql {

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     * @param string $filter_category selected.
     */
    function __construct($uniqueid, $filter_category = '') {
        parent::__construct($uniqueid);

        // Define the list of columns to show.
        $columns = array('title', 'roles', 'content', 'position');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array(
            get_string('announcement_title', 'block_announcements'),
            get_string('assignedroles', 'role'),
            get_string('content'),
            ''
        );
        $this->define_headers($headers);

        // Set sql
        $select = '*';
        $where = '';
        $params = array();

        // category filter
        $where .= 'category = :filter_category';
        $params['filter_category'] = $filter_category;

        $this->set_sql($select, "{".\block_announcements\announcement::$db_table."}", $where, $params);

        // Not downloadable
        $this->is_downloadable(false);

        // Sortable
        $this->sortable(false, 'position', SORT_ASC);

        // Columns cant't be hide
        $this->collapsible(false);

        // Caption
        $this->set_attribute('caption', get_string('announcements_manage', 'block_announcements'));
        $this->set_attribute('hidecaption', 'hide');
    }

    /**
     * This function is called for each data row to allow processing of the
     * username value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string .
     */
    function col_title($values) {
        $length = 30;
        $html = '';
        $stringDisplay = substr(strip_tags($values->title), 0, $length);
        if (strlen(strip_tags($values->title)) > $length) $stringDisplay .= ' ...';

        if ($values->enabled > 0) {
            $html .= '<i class="fa fa-eye icon" title="'.get_string('element_enable', 'block_announcements').'"></i>'.'&nbsp;';
        } else {
            $html .= '<i class="fa fa-eye-slash icon" title="'.get_string('element_enable', 'block_announcements').'"></i>'.'&nbsp;';
        }

        $html .= $stringDisplay;
        return $html;
    }

    function col_roles($values) {
        $roles = $values->roles;
        $html = '';
        if (!empty($roles)) {
            $rolesids = explode(',',$roles);
            $assignableroles = \block_announcements\manager::get_context_system_roles();
            foreach ($rolesids as $roleid) {
                $html .= $assignableroles[$roleid]. '<br/>';
            }
        }
        return $html;
    }


    /**
     * This function is called for each data row to allow processing of the
     * username value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string .
     */
    function col_position($values) {
        $id = $values->id;
        $position = $values->position;
        $category = $values->category;
        $html = '';
        $urledit = new \moodle_url('/blocks/announcements/manager/edit.php', array('id' => intval($id)));
        $urldel = new \moodle_url('/blocks/announcements/manager/del.php', array('id' => intval($id)));

        if ($position != 0) {
            $html .= '<a href="javascript:void(0);" id="announcement_up_'.$id.'">';
            $html .= '<i class="fa fa-arrow-up icon" title="'.get_string('up').'"></i>';
            $html .= '</a>';
        } else {
            $html .= '<i class="fa fa-arrow-up icon" style="color: grey;" title="'.get_string('up').'"></i>';
        }
        $html .= '&nbsp;';

        if (\block_announcements\manager::get_last_category_position($category) != $position) {
            $html .= '<a href="javascript:void(0);" id="announcement_down_'.$id.'">';
            $html .= '<i class="fa fa-arrow-down icon" title="'.get_string('down').'"></i>';
            $html .= '</a>';
        } else {
            $html .= '<i class="fa fa-arrow-down icon" style="color: grey;" title="'.get_string('down').'"></i>';
        }
        $html .= '&nbsp;';

        $html .= '<a href="'.$urledit->out().'">';
        $html .= '<i class="fa fa-pencil-square-o icon" title="'.get_string('edit').'"></i>';
        $html .= '</a>';

        $html .= '&nbsp;';

        $html .= '<a href="'.$urldel->out().'">';
        $html .= '<i class="fa fa-trash-o icon" title="'.get_string('delete').'"></i>';
        $html .= '</a>';

        return $html;
    }
}
