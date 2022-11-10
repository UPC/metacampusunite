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

function xmldb_block_announcements_upgrade($oldversion) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/blocks/announcements/db/upgradelib.php');

    if ($oldversion < 2017062000) {
        create_table_announcements();
        upgrade_plugin_savepoint(true, 2017062000, 'block', 'announcements');
    }

    if ($oldversion < 2019052100) {

    	// Define field roles to be added to announcements
    	$dbman = $DB->get_manager();
    	$table = new xmldb_table(\block_announcements\announcement::$db_table);
        $field = new xmldb_field('roles', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Conditionally launch add field roles
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2019052100, 'block', 'announcements');

    }

    if ($oldversion < 2020082812) {
        if (!upgrade_block_announcements_add_block_announcements_field()) {
            return false;
        }
        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2020082812, 'block', 'announcements');
    }


    return true;
}