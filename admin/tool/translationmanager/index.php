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
use tool_translationmanager;
use tool_translationmanager\form\search_form;
require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir.'/adminlib.php');

$page = optional_param('page', 0, PARAM_INT);
$lang = optional_param('searchlang', '', PARAM_ALPHA);
$pagefilter = optional_param('pagefilter', '', PARAM_TEXT);
$pagefilter = urldecode($pagefilter);
require_login();
require_capability('filter/fulltranslate:edittranslations', context_system::instance());
$url = new moodle_url('/admin/tool/edit.php');
$PAGE->set_url($url);
$PAGE->set_title('Full translate filter');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_translationmanager'));
$PAGE->requires->js_call_amd('tool_translationmanager/translationmanager', 'init');
$languages = get_string_manager()->get_list_of_translations();
$languagelinks = [];
foreach ($languages as $key => $value) {
    $languagelinks[] = new action_menu_link_secondary(
        new moodle_url($PAGE->url->out(), ['pagefilter' => urlencode($pagefilter), 'searchlang' => $key]), null, $value
    );
}
$actionmenu = new action_menu($languagelinks);
$actionmenu->set_menu_trigger(get_string('language'));
$actionmenu->set_action_label(get_string('language'));
$actionmenu->set_alignment(\action_menu::TR, \action_menu::TR);
echo '<div class="d-flex flex-row-reverse p-4"><h4>';
echo "<a href='".$CFG->wwwroot."/admin/tool/translationmanager/pages.php'>".get_string('backtolistofpages', 'tool_translationmanager')."</a>";
echo $OUTPUT->render($actionmenu);
echo '</h4></div>';

$search = '';
$mform = new search_form();

if ($fromform = $mform->get_data()) {
    //print_object($fromform);
    $search = $fromform->search;
}
$mform->display();
$translationtable = new tool_translationmanager\translationtable($lang, $pagefilter, $search);
$translationtable->pagesize = 50;
$translationtable->define_baseurl(new moodle_url('//admin/tool/translationmanager/index.php', ['page' => $page, 'searchlang' => $lang, 'pagefilter' => $page]));
$translationtable->out(50, false);
echo $OUTPUT->footer();