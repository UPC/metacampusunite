<?php
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

/**
 * Search form renderable.
 *
 * @package    block_search_courses
 * @copyright  2017 UPCnet
 */

namespace block_search_courses\output;
defined('MOODLE_INTERNAL') || die();

use help_icon;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Search form renderable class.
 *
 */
class search_form implements renderable, templatable {

    /** @var moodle_url The form action URL. */
    protected $actionurl;

    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     */
    public function __construct($courseid) {
        $this->actionurl = new moodle_url('/course/search.php');
    }

    public function export_for_template(renderer_base $output) {
        $data = [
            'actionurl' => $this->actionurl->out(false),
        ];
        return $data;
    }

}
