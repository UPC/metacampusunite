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

namespace block_announcements;

require_once($CFG->libdir.'/formslib.php');

/**
 * Class announcement_form
 *
 * @copyright 2017 UPCNet
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class announcement_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform =& $this->_form;

        $mform->disable_form_change_checker();

        $mform->addElement('header','general', get_string('general', 'form'));

        // Title
        $mform->addElement('text', 'title', get_string('announcement_title', 'block_announcements'));
        $mform->setType('title', PARAM_RAW);
        $mform->setDefault('title', '');

        // Content
        $editoroptions = array(
            'subdirs'=>0,
            'maxbytes'=>0,
            'maxfiles'=>0,
            'changeformat'=>0,
            'context'=>null,
            'noclean'=>0,
            'trusttext'=>0
        );
        $mform->addElement('editor', 'content', get_string('content'), null, $editoroptions);
        $mform->setType('content', PARAM_RAW);
        $mform->setDefault('content', array('text' => '', 'format' => 1));

        // Image
        $mform->addElement('static', 'currentpicture', get_string('currentpicture'));
        $mform->addElement('checkbox', 'deletepicture', get_string('announcement_delete_img', 'block_announcements'));
        $filepickeroptions = array();
        $filepickeroptions['accepted_types'] = array('image');
        $filepickeroptions['maxbytes'] = get_max_upload_file_size();
        $filepickeroptions['component'] = \block_announcements\announcement::$component;
        $filepickeroptions['filearea'] = \block_announcements\announcement::$file_area;
        $mform->addElement('filepicker', 'img', get_string('announcement_img', 'block_announcements'), null, $filepickeroptions);

        // Advanced elements

        // Icon
        $mform->addElement('text', 'icon', get_string('marketingicondesc', 'block_announcements'));
        $mform->setType('icon', PARAM_RAW);
        $mform->setDefault('icon', '');
        $mform->setAdvanced('icon', true);
        $mform->addHelpButton('icon','marketingicondesc', 'block_announcements');

        // alt
        $mform->addElement('text', 'alt', 'Alt');
        $mform->setType('alt', PARAM_RAW);
        $mform->setDefault('alt', '');
        $mform->setAdvanced('alt', true);


        // Url
        $mform->addElement('text', 'url', get_string('url'));
        $mform->setType('url', PARAM_URL);
        $mform->setDefault('url', '');
        $mform->setAdvanced('url', true);

        // Link_Target
        $options = \block_announcements\announcement::get_values_for_target();
        $mform->addElement('select', 'link_target', get_string('url_target', 'block_announcements'), $options);
        $mform->setAdvanced('link_target', true);

        $mform->addElement('header','visibility', get_string('visible', 'moodle'));

        // Enabled
        $mform->addElement('checkbox', 'enabled', get_string('element_enable', 'block_announcements'), '&nbsp;');

        // category
        // Two options: select from selector or create new one
        $all_categorys = \block_announcements\manager::get_all_categorys();
        $choices = array();

        $choices[''] = get_string('no_category', 'block_announcements');
        foreach ($all_categorys as $category) {
            if ($category == '') continue;
            $choices[$category] = $category;
        }
        $mform->addElement('select', 'filter_category', get_string('announcement_selected_category', 'block_announcements'), $choices, array('id' => 'filter_category'));

        $mform->addElement('text', 'category', get_string('announcement_new_category', 'block_announcements'));
        $mform->setType('category', PARAM_RAW);
        $mform->setDefault('category', '');
        $mform->addHelpButton('category','createcategory', 'block_announcements');

        // Roles at system contex
        $assignableroles = \block_announcements\manager::get_context_system_roles();

        if(isset($assignableroles) && !empty($assignableroles)) {
            $typeitem = array();
            foreach ($assignableroles as $roleid => $rolename) {
                 $typeitem[] = &$mform->createElement('advcheckbox',$roleid, '', $rolename, array('group'=>1), array(0,1));
            }
            $mform->addGroup($typeitem, 'roles',get_string('announcement_assigntoroles', 'block_announcements'));
            //$this->add_checkbox_controller(1);
        }

        $this->add_action_buttons();
    }

    /**
     * Extra validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate data in object
        $announcement = new \block_announcements\announcement();
        $announcement->set_title($data['title']);
        $announcement->set_content($data['content']['text']);
        $announcement->set_url($data['url']);
        $announcement->set_link_target($data['link_target']);
        $announcement->set_category($data['category']);
        $errors_object = $announcement->validate();

        return array_merge($errors, $errors_object);
    }

    public function set_data($data){
        parent::set_data($data);

        // seleccionem la categoria
        //$selectedcategory = array('filter_category' => $data['category']);
        //parent::set_data($selectedcategory);

        // Marquem els rols seleccionats
        if (!empty($data['roles'])) {
            $roles = explode(',', $data['roles']);
            if (!empty($roles)) {
                $default_values = array();
                foreach ($roles as $key => $value) {
                    $default_values['roles['.$value.']'] = 1;
                }
                parent::set_data($default_values);
            }
        }
    }

    public function definition_after_data(){
        //$mform =& $this->_form;
        //$mform->setDefault('filter_category','555555');
    }
}