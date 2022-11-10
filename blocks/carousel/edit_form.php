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

defined('MOODLE_INTERNAL') || die();

/**
 * Form for editing carousel block settings
 *
 * @package    block_carousel
 * @copyright  2017 UPCnet
 * @author     Ferran Recio <ferran.recio@upcnet.es>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_carousel_edit_form extends block_edit_form {

    var $maxelements = 3; //#TODO# remove

    protected function specific_definition($mform) {
        global $CFG,$OUTPUT;

        //truco molt guarro pero ja no se com fer per accedir al form
        $this->block->mform = $mform;

        $maxelements = $this->maxelements;
        $height = (isset($this->block->config->height))?$this->block->config->height:250;


        // Categories
        $categories = \block_carousel\manager::get_all_categories();
        sort($categories);
        $options = array();
        $options[''] = get_string('no_category', 'block_carousel');
        foreach ($categories as $category) {
            if ($category == '') continue;
            $options[$category] = $category;
        }
        $mform->addElement('select', 'config_category', get_string('category'), $options);

        
        //opciones de visualitzación
        $mform->addElement('text', 'config_height', get_string('height', 'block_carousel'));
        $mform->setDefault('config_height', 250);
        $mform->setType('config_height', PARAM_INT);

    }
}