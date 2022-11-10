<?php

require_once('upgradelib.php');

function xmldb_block_carousel_upgrade($oldversion) {
    $result = TRUE;

    if ($oldversion < 2019052101) {
        if (!upgrade_block_carousel_add_block_carousel_table()) {
            return false;
        }
        if (!upgrade_block_carousel_migrate_block_carousel()) {
            return false;
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2019052101, 'block', 'carousel');
    }

    if ($oldversion < 2020082812) {
        if (!upgrade_block_carousel_add_block_carousel_field()) {
            return false;
        }
        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2020082812, 'block', 'carousel');
    }


    return $result;
}
