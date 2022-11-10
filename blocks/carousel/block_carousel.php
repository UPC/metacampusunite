<?php
// ------------------------
// @FUNC F029 AT: Block carousel.
// Block que mostra imatges i text en format carousel, es a dir, els contiguts del carousel van rotan, mostranse durant un estona fins que es pasa al següent, tambe es avaçar de contingut manualment.
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

/**
 * Block displaying information about current logged-in user.
 *
 * This block can be used as anti cheating measure, you
 * can easily check the logged-in user matches the person
 * operating the computer.
 *
 * @package    block_carousel
 * @copyright  2010 Remote-Learner.net
 * @author     Olav Jordan <olav.jordan@remote-learner.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/carousel/lib.php');

/**
 * Displays the carousel information.
 *
 * @copyright  2017 UPCnet
 * @author     Ferran Recio <ferran.recio@upcnet.es>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_carousel extends block_base {
    var $maxelements = 3;

    /**
     * block initializations
     */
    public function init() {
        $this->title   = get_string('pluginname', 'block_carousel');
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
            return '';      // Never useful unless you are logged in as real users
        }*/

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $course = $this->page->course;
        if (!isset($this->config)) {
            $this->config = new stdClass;
            $this->config->height = 250;
            $this->config->category = '';
        }

        $data = (object)[
            'carouselid'=>'MoodleCarousel',
            'elements' => [],
            'carouselheight'=>$this->config->height
        ];


		$userrolesatsystemcontext = null;
		if (!is_siteadmin()) {
			// $userrolesatsystemcontext = array_keys(get_user_roles(\context_system::instance()));
			$userrolesatsystemcontext = get_user_roles(\context_system::instance());
			$userrolesatsystemcontext = array_map(function($a) {return $a->roleid;}, $userrolesatsystemcontext);
		}
        $carouselslides = block_carousel_get_images($this->config->category, $userrolesatsystemcontext);

        foreach ($carouselslides as $carouselslide) {
            if (!$carouselslide->enabled) continue;
            $element = (object) [
                'url'=>format_text($carouselslide->imgsrc, FORMAT_PLAIN),
                'title'=>format_text($carouselslide->title, FORMAT_HTML),
                'content'=>format_text($carouselslide->content, FORMAT_HTML),
                'alt'=>format_text($carouselslide->alt, FORMAT_HTML),
                'url_link'=>format_text($carouselslide->url, FORMAT_HTML),
                'url_link_target'=>$carouselslide->link_target,
                ];
            if (!count($data->elements)) $element->active = 'active';
            //$element->active = ($i==1)?'active':'';
            $data->elements[] = $element;
            //echo $OUTPUT->render_from_template('block_carousel/carousel', $data);
        }

        if (empty($data->elements) && !$this->page->user_is_editing()) return $this->content;
        if (count($data->elements)>1) $data->showindicators = 'true';

        $data->button_carousel = null;
        if ($this->page->user_is_editing() && has_capability('block/carousel:addinstance', $this->context)) {
            $button_carousel_url = new moodle_url('/blocks/carousel/manager/index.php?filter_category='.$this->config->category, array());
            $data->button_carousel = $button_carousel_url->out();
        }

        $this->content->text.= $OUTPUT->render_from_template('block_carousel/carousel', $data);

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
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        parent::instance_config_save($data);
    }

    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_carousel');
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        return true;
    }

    /**
     * allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        //allow more than one instance on a page
        return false;
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

}
