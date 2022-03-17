<?php

namespace theme_metacampus\output\core;

defined('MOODLE_INTERNAL') || die();

use core_course_category;

require_once($CFG->dirroot . '/course/renderer.php');

class course_renderer extends \core_course_renderer {

    public function course_category($category) {
        return parent::course_category($category);
    }

}