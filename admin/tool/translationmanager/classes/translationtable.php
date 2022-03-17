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
 * Translation manager
 *
 * @package    tool
 * @subpackage translationmanager
 * @copyright  2020 Farhan Karmali <farhan6318@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translationmanager;

use tool_wp\language;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class for the displaying the translation table.
 *
 * @package    tool
 * @subpackage translationmanager
 * @copyright  2020 Farhan Karmali <farhan6318@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translationtable extends \table_sql {

    /**
     * @var string language
     */
    protected $lang;

    protected $pagefilter;

    public function __construct($lang = '', $pagefilter = '', $search = '') {
        parent::__construct('translations-table');
        // Define the list of columns to show.
        $columns = ['sourcetext', 'translation', 'timecreated', 'timemodified', 'lang', 'edit'];
        $this->define_columns($columns);
        // Define the titles of columns to show in header.
        $headers = ['Original', 'Translation', 'Created', 'Modified', 'Lang', get_string('edit')];
        $this->define_headers($headers);
        $this->is_collapsible = false;
        $this->sortable(false);
        $this->use_pages = true;
        $this->lang = $lang;
        $this->pagefilter = $pagefilter;
        $this->search = $search;
    }

    public function col_edit($data) {
        $params = ['id' => $data->id];
        if ($this->lang) {
            $params['lang'] = $this->lang;
        }
        if ($this->pagefilter) {
            $params['pagefilter'] = $this->pagefilter;
        }
        $url = new \moodle_url('edit.php', $params);
        return \html_writer::link($url, get_string("edit"), ['data-action' => "translation-edit", 'data-recordid' => $data->id]);
    }

    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;
        $params = [];
        $where = [];
        if ($this->lang != '') {
            $params['lang'] = $this->lang;
            $where[] = $DB->sql_like('url', ':lang');
        }
        if ($this->pagefilter != '') {
            $params['url'] = $this->pagefilter;
            $where[] = 'url = :url';
        }
        if ($this->search != '') {
            $where[] = $DB->sql_like('sourcetext', ':search', false);
            $params['search'] = "%".$DB->sql_like_escape($this->search)."%";
        }
        $wherep[] = 'hidefromtable != 1';
        $wherestr = implode(" AND ", $where);
        $records = $DB->get_records_select('filter_fulltranslate', $wherestr, $params, 'timemodified ASC', '*', $this->get_page_start(), $this->get_page_size());
        $total = $DB->count_records_select('filter_fulltranslate', $wherestr, $params);
        foreach ($records as $record) {
            $record->edit = '';
            $record->timecreated = userdate_htmltime($record->timecreated);
            $record->timemodified = $record->timemodified ? userdate_htmltime($record->timemodified) : 'Never modified';
            $this->rawdata[] = $record;
        }
        $this->pagesize($pagesize, $total);
    }

}