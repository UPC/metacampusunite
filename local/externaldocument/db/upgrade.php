<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_externaldocument_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021042600) {

        // Define table extdoc_t_course to be renamed to NEWNAMEGOESHERE.
        $table = new xmldb_table('extdocument_document');

        // Launch rename table for extdoc_t_course.
        $dbman->rename_table($table, 'extdoc_t_course');

        // Externaldocument savepoint reached.
        upgrade_plugin_savepoint(true, 2021042600, 'local', 'externaldocument');
    }

    if ($oldversion < 2021050301) {

        // Define field language to be added to extdoc_t_course.
        $table = new xmldb_table('extdoc_t_course');
        $field = new xmldb_field('language', XMLDB_TYPE_TEXT, null, null, null, null, null, 'url');

        // Conditionally launch add field language.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('tags', XMLDB_TYPE_TEXT, null, null, null, null, null, 'language');

        // Conditionally launch add field tags.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('coursecredits', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'language');

        // Conditionally launch add field coursecredits.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('starttime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'coursecredits');

        // Conditionally launch add field starttime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('endtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'starttime');

        // Conditionally launch add field endtime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Externaldocument savepoint reached.
        upgrade_plugin_savepoint(true, 2021050301, 'local', 'externaldocument');
    }

    if ($oldversion < 2021061000) {

        // Define table extdoc_t_webpage to be created.
        $table = new xmldb_table('extdoc_t_webpage');

        // Adding fields to table extdoc_t_webpage.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('title', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('description1', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('description2', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('language', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('languageofwork', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('tags', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table extdoc_t_webpage.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for extdoc_t_webpage.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Externaldocument savepoint reached.
        upgrade_plugin_savepoint(true, 2021061000, 'local', 'externaldocument');
    }

    if ($oldversion < 2021061001) {

        // Define table extdoc_t_person to be created.
        $table = new xmldb_table('extdoc_t_person');

        // Adding fields to table extdoc_t_person.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('title', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('description1', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('description2', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('givenname', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('familyname', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('employer', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('employerstarttime', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('employerendtime', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('tags', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('occupation', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('orcidid', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table extdoc_t_person.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for extdoc_t_person.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Externaldocument savepoint reached.
        upgrade_plugin_savepoint(true, 2021061001, 'local', 'externaldocument');
    }

    if ($oldversion < 2021061700) {

        // Define table extdoc_t_course to be renamed to NEWNAMEGOESHERE.
        $table = new xmldb_table('extdoc_t_asset');

        // Launch rename table for extdoc_t_course.
        $dbman->rename_table($table, 'extdoc_t_course');

        // Externaldocument savepoint reached.
        upgrade_plugin_savepoint(true, 2021061700, 'local', 'externaldocument');
    }

    if ($oldversion < 2022012000) {
        // Changing type of field employerstarttime on table extdoc_t_person to int.
        $table = new xmldb_table('extdoc_t_person');
        $field = new xmldb_field('employerstarttime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'employer');

        // Launch change of type for field employerstarttime.
        $dbman->change_field_type($table, $field);

        // Changing type of field employerendtime on table extdoc_t_person to int.
        $field = new xmldb_field('employerendtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'employerstarttime');

        // Launch change of type for field employerendtime.
        $dbman->change_field_type($table, $field);

        // Externaldocument savepoint reached.
        upgrade_plugin_savepoint(true, 2022012000, 'local', 'externaldocument');
    }

    return true;
}