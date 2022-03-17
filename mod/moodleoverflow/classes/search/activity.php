<?php

/**
 * Moodleoverflow activities search area
 *
 * @package    mod_moodleoverflow
 * @copyright  2021 IthinkUPC
 */

namespace mod_moodleoverflow\search;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodleoverflow activities search area.
 *
 * @package    mod_moodleoverflow
 * @copyright  2021 IthinkUPC
 */
class activity extends \core_search\base_activity {

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }
}
