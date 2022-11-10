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

class manager {

    public static function get_announcements_blocks_in_context($contextid) {
      global $DB;
        return $DB->get_records('block_instances', array('parentcontextid' => $contextid, 'blockname'=>'announcements'));
    }

    /**
     * Get all categorys of the database.
     *
     * @return array Array of strings
     */
    public static function get_all_categorys() {
        global $DB;

        $return = array();

        $categorys = $DB->get_records_sql(
            "SELECT DISTINCT category FROM {".\block_announcements\announcement::$db_table."}"
        );
        if(empty($categorys)) return $return;

        foreach ($categorys as $category){
            array_push($return, $category->category);
        }

        return $return;
    }

    /**
     * Get last position (number) searching by category.
     *
     * @param  string $category category to search
     * @return integer The position.
     */
    public static function get_last_category_position($category = '') {
        global $DB;

        $sql = "
            SELECT MAX(position) FROM {".\block_announcements\announcement::$db_table."}
            WHERE category = :category_val
        ";
        $position = $DB->get_field_sql($sql, array('category_val' => $category), IGNORE_MISSING);

        return intval($position);
    }

    /**
     * Move 1 position block
     * @param  integer $id Id of the block
     * @param  integer $up_or_down 0 -> Up ; != 0 ->down
     * @return int new position or null
     */
    public static function move_block($id = 0, $up_or_down = 0) {
        global $DB;
        $newposition = null;
        $action = null;

        if (intval($up_or_down) === 0) {
            $action = 'up';
        } else {
            $action = 'down';
        }

        $announcement = new \block_announcements\announcement();
        if ($announcement->get($id)) {
            $currentposition = $announcement->get_position();
            $lastposition = self::get_last_category_position($announcement->get_category());

            if ( ($action == 'up' && $currentposition > 0) || ($action == 'down' && $currentposition < $lastposition) ) {

                switch ($action) {
                    case 'up':
                        $newposition = $currentposition - 1;
                        break;
                    case 'down':
                        $newposition = $currentposition + 1;
                        break;
                    default:
                        $newposition = null;
                        break;
                }

                $counter = 0;
                $blocks = self::get_blocks_by_category($announcement->get_category(), 'position');
                if (!empty($blocks)) {
                    foreach ($blocks as $block) {
                        $nextposition = $counter;

                        if($currentposition == $nextposition) {
                            $nextposition = $newposition;
                        } else if(($currentposition > $nextposition) && ($newposition <= $nextposition)) {
                            $nextposition++;
                        } else if(($currentposition < $nextposition) && ($newposition >= $nextposition)) {
                            $nextposition--;
                        }

                        $block->set_position($nextposition);
                        $block->save();
                        $counter++;
                    }
                }
            }
        }

        return $newposition;
    }

    /**
     * Get blocks by category.
     *
     * @param  string $category category to search
     * @param  string $order_by Field to order (ASC)
     * @return array Array of blocks.
     */
    public static function get_blocks_by_category($category = '', $order_by = 'position') {
        global $DB;
        $blocks = array();

        $records = $DB->get_records(\block_announcements\announcement::$db_table, array('category' => $category), $order_by);
        if ($records) {
            foreach ($records as $record) {
                $block = new \block_announcements\announcement();
                if ($block->get($record->id)) {
                    array_push($blocks, $block);
                }
            }
        }

        return $blocks;
    }

    /**
     * Get enabled blocks by category.
     *
     * @param  string $category category to search
     * @param  string $order_by Field to order (ASC)
     * @return array Array of blocks.
     */
    public static function get_enabled_blocks_by_category($category = '', $order_by = 'position', $id = '') {
        global $DB;
        $blocks = array();

        if (!$id) {
            $records = $DB->get_records(\block_announcements\announcement::$db_table, array('category' => $category, 'enabled' => 1), $order_by);
        } else {
            $records = $DB->get_records(\block_announcements\announcement::$db_table, array('category' => $category, 'enabled' => 1, 'id' => $id), $order_by);
        }

        if ($records) {
            foreach ($records as $record) {
                $block = new \block_announcements\announcement();
                if ($block->get($record->id)) {
                    array_push($blocks, $block);
                }
            }
        }

        return $blocks;
    }

    public static function get_context_system_roles(){
        global $DB;

        $context = \context_system::instance();
        $params = array('contextlevel' => $context->contextlevel);
        

        if ($coursecontext = $context->get_course_context(false)) {
            $params['coursecontext'] = $coursecontext->id;
        } else {
            $params['coursecontext'] = 0; // no course aliases
            $coursecontext = null;
        }

        $sql = "SELECT r.id, r.name, r.shortname, rn.name AS coursealias
              FROM {role} r
              JOIN {role_context_levels} rcl ON (rcl.contextlevel = :contextlevel AND r.id = rcl.roleid)
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
          ORDER BY r.sortorder ASC";
        $roles = $DB->get_records_sql($sql, $params);

        $rolenames = role_fix_names($roles, $coursecontext, ROLENAME_ORIGINAL, true);

        return $rolenames;

    }
}
