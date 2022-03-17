<?php

function xmldb_filter_fulltranslate_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2020042801) {

        // Define table filter_fulltranslate to be created.
        $table = new xmldb_table('filter_fulltranslate');

        // Adding fields to table filter_fulltranslate.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('hashkey', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('textformat', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('lastaccess', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('lang', XMLDB_TYPE_CHAR, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('automatic', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('translation', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table filter_fulltranslate.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table filter_fulltranslate.
        $table->add_index('hashkeyindex', XMLDB_INDEX_NOTUNIQUE, ['hashkey']);

        // Conditionally launch create table for filter_fulltranslate.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Fulltranslate savepoint reached.
        upgrade_plugin_savepoint(true, 2020042801, 'filter', 'fulltranslate');
    }

    if ($oldversion < 2020042900) {

        // Define field sourcetext to be added to filter_fulltranslate.
        $table = new xmldb_table('filter_fulltranslate');
        $field = new xmldb_field('sourcetext', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'hashkey');

        // Conditionally launch add field sourcetext.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Fulltranslate savepoint reached.
        upgrade_plugin_savepoint(true, 2020042900, 'filter', 'fulltranslate');
    }

    if ($oldversion < 2020042901) {
        // Fulltranslate savepoint reached.
        upgrade_plugin_savepoint(true, 2020042901, 'filter', 'fulltranslate');
    }

    if ($oldversion < 2020042903) {
        // Fulltranslate savepoint reached.
        upgrade_plugin_savepoint(true, 2020042903, 'filter', 'fulltranslate');
    }

    if ($oldversion < 2020042904) {

        // Changing precision of field lang on table filter_fulltranslate to (10).
        $table = new xmldb_table('filter_fulltranslate');
        $field = new xmldb_field('lang', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, 'lastaccess');

        // Launch change of precision for field lang.
        $dbman->change_field_precision($table, $field);

        // Fulltranslate savepoint reached.
        upgrade_plugin_savepoint(true, 2020042904, 'filter', 'fulltranslate');
    }

    if ($oldversion < 2020042905) {

        // Define field hidefromtable to be added to filter_fulltranslate.
        $table = new xmldb_table('filter_fulltranslate');
        $field = new xmldb_field('hidefromtable', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'translation');

        // Conditionally launch add field hidefromtable.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Fulltranslate savepoint reached.
        upgrade_plugin_savepoint(true, 2020042905, 'filter', 'fulltranslate');
    }

    if ($oldversion < 2020042906) {
        // Fulltranslate savepoint reached.
        upgrade_plugin_savepoint(true, 2020042906, 'filter', 'fulltranslate');
    }



}