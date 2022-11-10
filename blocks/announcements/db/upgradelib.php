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

function create_table_announcements() {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/blocks/announcements/classes/announcement.php');

    $dbman = $DB->get_manager();

    $table = new xmldb_table(\block_announcements\announcement::$db_table);

    // Adding fields to table at_mylog
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, null, null, '');
    $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, null, null, '');
    $table->add_field('link_target', XMLDB_TYPE_CHAR, '255', null, null, null, '');
    $table->add_field('category', XMLDB_TYPE_CHAR, '255', null, null, null, '');
    $table->add_field('img', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 0);
    $table->add_field('icon', XMLDB_TYPE_CHAR, '255', null, null, null, '');
    $table->add_field('position', XMLDB_TYPE_INTEGER, '10', null, null, null, 0);
    $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, null, null, 0);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, null, null, 0);
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '20', null, null, null, 0);
    $table->add_field('roles', XMLDB_TYPE_CHAR, '255', null, null, null, null);

    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_index('category', XMLDB_INDEX_NOTUNIQUE, array('category'));
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    return true;
}

function upgrade_block_announcements_add_block_announcements_field(){
    global $DB, $CFG;
    require_once($CFG->dirroot.'/blocks/announcements/classes/announcement.php');

    $alt_field = new xmldb_field('alt');
    $alt_field->set_attributes(XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table = new xmldb_table(\block_announcements\announcement::$db_table);

    $dbman = $DB->get_manager();

    if (!$dbman->field_exists($table, $alt_field)) {
        $dbman->add_field($table, $alt_field);
    }

    return true;
}
