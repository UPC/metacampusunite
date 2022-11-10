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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the announcements information.
 *
 * @copyright  2017 UPCnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_announcements extends block_base {
    var $maxelements = 3;

    /**
     * block initializations
     */
    public function init() {
        $this->title   = get_string('pluginname', 'block_announcements');
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        /*if (!isloggedin() or isguestuser()) {
            return ''; // Never useful unless you are logged in as real users
        }*/

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        $this->layout = '';
        $this->position = '';

        if (!isset($this->config)) {
            $this->config = new stdClass;
        }
        if (!isset($this->config->category)) $this->config->category = '';
        if (!isset($this->config->view_link_other_announcements)) $this->config->view_link_other_announcements = 0;
        if (isset($this->config->maxelements)) $this->maxelements = $this->config->maxelements;
        if (isset($this->config->position)) $this->position = $this->config->position;
        if (isset($this->config->layout)) $this->layout = $this->config->layout;

        // Get announcements
        $announcements = \block_announcements\manager::get_enabled_blocks_by_category($this->config->category);
        //$announcements = array_slice($announcements, 0, $this->maxelements);

        // Fill template
        $data = new stdClass;
        $data->layout = '';
        $data->announcementsid = 'Moodleannouncements';

        $data->position = $this->position;
        if ($this->layout == 2) $data->layout = $this->layout;

        $data->link_other_announcements = null;
        if ($this->config->view_link_other_announcements
            && (
                isloggedin() || $this->instance->parentcontextid == 2
            )
        ) {
            $link_other_announcements_url = new moodle_url('/blocks/announcements/viewall.php', array('category' => $this->config->category));
            $data->link_other_announcements = $link_other_announcements_url->out();
        }

        $data->button_announcements = null;
        if ($this->page->user_is_editing() && has_capability('block/announcements:addinstance', $this->context)) {
            $button_announcements_url = new moodle_url('/blocks/announcements/manager/index.php?filter_category='.$this->config->category, array());
            $data->button_announcements = $button_announcements_url->out();
        }

        $data->elements = array();

        // Get user roles in site context
        $userrolesatsystemcontext = get_user_roles(\context_system::instance());
        foreach ($announcements as $announcement) {

            if (count($data->elements) == $this->maxelements){
                break;
            }

            if (!$announcement->is_visible_for_user_with_roles($userrolesatsystemcontext)){
                continue;
            }

            $button_edit_announcement = null;
            if ($this->page->user_is_editing() && has_capability('block/announcements:addinstance', $this->context)) {
                $button_edit_announcement_url = new moodle_url('/blocks/announcements/manager/edit.php', array('id' => $announcement->get_id(), 'filter_category' => $announcement->get_category()));
                $button_edit_announcement = $button_edit_announcement_url->out();
            }
            $element = (object) [
                'imageurl' => $announcement->get_img_url(),
                'title' => format_text($announcement->get_title(), FORMAT_HTML, array('trusted' => true, 'noclean' => true)),
                'content' => format_text($announcement->get_content(), FORMAT_HTML, array('trusted' => true, 'noclean' => true)),
                'url' => format_text($announcement->get_url(), FORMAT_PLAIN, array('trusted' => true, 'noclean' => true)),
                'target' => $announcement->get_link_target(),
                'alt'=>format_text($announcement->get_alt(), FORMAT_HTML),
                'button_edit_announcement' => $button_edit_announcement,
                'fontawesome_icon' => format_text($announcement->get_icon(), FORMAT_HTML, array('trusted' => true, 'noclean' => true)),
                'timecreated' => userdate($announcement->timecreated, get_string('strftimedatefullshort', 'core_langconfig')),
                'timemodified' => userdate($announcement->timemodified, get_string('strftimedatefullshort', 'core_langconfig')),
            ];
            $data->elements[] = $element;
        }

        if (count($data->elements)) {
            $data->colsperrow = round(12/count($data->elements));
        } else {
            $data->colsperrow = 12;
        }

        //midem si tenim un inclusion o no
        if (function_exists('atenea_include_inclusion')) {
            ob_start();
            $result = atenea_include_inclusion ('block_announcements', array('config'=> $this->config, 'data' => $data));
            $output = ob_get_contents();
            ob_end_clean();
            if (!empty($output)) {
                $this->content->text .= $output;
                return $this->content;
            }
        }
        // si no tneim res a posar deixem el text en blanc
        if (!empty($data->elements) || $data->button_announcements || $data->link_other_announcements) {
            if($data->position == 1){
                $this->content->text .= $OUTPUT->render_from_template('block_announcements/announcement_text_top', $data);
            }else{
                if($data->position == 2 || $data->layout == 2){
                    $this->content->text .= $OUTPUT->render_from_template('block_announcements/announcement_img_top', $data);
                }else{
                    $this->content->text .= $OUTPUT->render_from_template('block_announcements/announcements', $data);
                }
            }
        }
        // End fill template
        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        //allow more than one instance on a page
        return true;
    }

    /**
     * allow instances to have their own configuration
     *
     * @return boolean
     */
    function instance_allow_config() {
        //allow instances to have their own configuration
        return true;
    }

    /**
     * instance specialisations (must have instance allow config true)
     *
     */
    public function specialization() {
        if (!$this->page->user_is_editing()) $this->title = '';
    }

    public function applicable_formats() {
        return array(
            'admin' => false,
            'site-index' => true,
            'course-view' => false,
            'mod' => false,
            'my' => true
        );
    }

    /**
     * post install configurations
     *
     */
    public function after_install() {
    }

    /**
     * post delete configurations
     *
     */
    public function before_delete() {
    }

    /*
     * Add custom html attributes to aid with theming and styling
     *
     * @return array
     */
    function html_attributes() {
        global $CFG;

        $attributes = parent::html_attributes();

        if (!empty($this->config->classes)) {
            $attributes['class'] .= ' '.$this->config->classes;
        }

        return $attributes;
    }

}
