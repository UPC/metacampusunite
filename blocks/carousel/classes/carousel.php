<?php
// ------------------------
// @FUNC F030 AT: Block carousel.
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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

namespace block_carousel;

class carousel {

    public static $db_table = 'block_carousel';
    public static $component = 'block_carousel';
    public static $file_area = 'carousel';

    private $id;
    private $title;
    private $content;
    private $alt;
    private $url;
    private $link_target;
    private $category;
    private $img;
    private $icon;
    private $position;
    private $enabled;
    private $timecreated;
    private $timemodified;
    private $roles;

    function __construct() {
        $this->id = 0;
        $this->title = '';
        $this->content = '';
        $this->alt = '';
        $this->url = '';
        $this->link_target = '';
        $this->category = '';
        $this->img = 0;
        $this->icon = '';
        $this->position = 0;
        $this->enabled = 1;
        $this->timecreated = 0;
        $this->timemodified = 0;
        $this->roles = '';
    }

    /**
     * Get database data and fill it to object.
     *
     * @param  integer $id Id of the block
     * @return boolean true or false.
     */
    public function get($id = 0) {
        global $DB;

        if(!$data = $DB->get_record(self::$db_table, array('id' => $id))) {
            return false;
        }

        $this->id = isset($data->id) ? $data->id : 0;
        $this->title = isset($data->title) ? $data->title : '';
        $this->content = isset($data->content) ? $data->content : '';
        $this->alt = isset($data->alt) ? $data->alt : '';
        $this->url = isset($data->url) ? $data->url : '';
        $this->link_target = isset($data->link_target) ? $data->link_target : '';
        $this->category = isset($data->category) ? $data->category : '';
        $this->img = isset($data->img) ? $data->img : 0;
        $this->icon = isset($data->icon) ? $data->icon : '';
        $this->position = isset($data->position) ? $data->position : 0;
        $this->enabled = isset($data->enabled) ? $data->enabled : 1;
        $this->timecreated = isset($data->timecreated) ? $data->timecreated : 0;
        $this->timemodified = isset($data->timemodified) ? $data->timemodified : 0;
        $this->roles = isset($data->roles) ? $data->roles : '';

        return true;
    }

    /**
     * Get valid values for link_target attribute.
     *
     * @return array.
     */
    public static function get_values_for_target() {
        $options = array(
            '_self' => get_string('url_self', 'block_carousel'),
            '_blank' => get_string('url_blank', 'block_carousel'),
        );
        return $options;
    }

    /**
     * Save object into DB.
     *
     * @return Bool True or false.
     */
    public function save() {
        global $DB;

        $errors = $this->validate();
        if (!empty($errors)) return false;

        if ($this->id > 0) {
            $result = $this->update_object_to_db();
            $this->get($this->id);
            return $result;
        } else {
            // Insert new object in the position 0
            $newid = $this->insert_object_to_db();

            if (!$newid) return false;
            $this->get($newid);

            // Add +1 position to other items with the same category
            $sql = "UPDATE {".self::$db_table."} SET position = position + 1 WHERE category = :current_category AND id !=".$newid;
            $DB->execute($sql, array('current_category' => $this->category));

            return true;
        }
    }

    /**
     * Insert object into DB
     *
     * @return new id or false
     */
    private function insert_object_to_db(){
        global $DB;

        $new_object = new \stdClass();
        $new_object->title  = $this->title;
        $new_object->content  = $this->content;
        $new_object->alt  = $this->alt;
        $new_object->img  = $this->img;
        $new_object->url  = $this->url;
        $new_object->link_target  = $this->link_target;
        // $new_object->icon  = $this->icon; //#DEBUG# remove if not using
        // $new_object->position  = $this->position; //#DEBUG# remove if not using
        $new_object->enabled  = $this->enabled;
        $new_object->category  = $this->category;
        // $new_object->timecreated  = time(); //#DEBUG# remove if not using
        // $new_object->timemodified  = time(); //#DEBUG# remove if not using
        $new_object->roles = $this->roles;

        // Insert into DB
        $new_id = $DB->insert_record(self::$db_table, $new_object);

        if(!$new_id) {
            return false;
        }

        return $new_id;
    }

    /**
     * Update object into DB
     *
     * @return boolean
     */
    private function update_object_to_db() {
        global $DB;

        $updated_object = new \stdClass();
        $updated_object->id = $this->id;
        $updated_object->title  = $this->title;
        $updated_object->content  = $this->content;
        $updated_object->alt  = $this->alt;
        $updated_object->url  = $this->url;
        $updated_object->link_target  = $this->link_target;
        $updated_object->category  = $this->category;
        $updated_object->img  = $this->img;
        $updated_object->icon  = $this->icon;
        $updated_object->position  = $this->position;
        $updated_object->enabled  = $this->enabled;
        $updated_object->timemodified  = time();
        $updated_object->roles = $this->roles;

        // Update into db
        $result = $DB->update_record(self::$db_table, $updated_object);

        if(!$result) {
            return false;
        }

        return true;
    }

    /**
     * Delete object into DB
     *
     * @return boolean
     */
    private function delete_object_to_db(){
        global $DB;

        return $DB->delete_records(self::$db_table, array('id' => $this->id));
    }

    /**
     * Validate params
     *
     * @return Array with key=param and value=error text.
     */
    public function validate() {
        $errors = array();

        // Validate URL
        if (!empty($this->url)) {
            if (filter_var($this->url, FILTER_VALIDATE_URL) === FALSE) {
                $errors['url'] = get_string('error_valid_url', 'block_carousel');
            }
        }

        // Check target
        $options = self::get_values_for_target();
        $keys = array_keys($options);
        if (!in_array($this->link_target, $keys)) {
            $errors['link_target'] = get_string('error_link_target', 'block_carousel');
        }

        return $errors;
    }

    /**
     * Get id of the object.
     *
     * @return integer Id of the object.
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get title of the object.
     *
     * @return string title of the object.
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Get content of the object.
     *
     * @return string content of the object.
     */
    public function get_content() {
        return $this->content;
    }

    /**
     * Get alt of the object.
     *
     * @return string alt of the object.
     */
    public function get_alt() {
        return $this->alt;
    }

    /**
     * Get url of the object.
     *
     * @return string url of the object.
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Get img of the object.
     *
     * @return string img of the object.
     */
    public function get_img() {
        return $this->img;
    }

    /**
     * Get link_target of the object.
     *
     * @return string link_target of the object.
     */
    public function get_link_target() {
        return $this->link_target;
    }

    /**
     * Get category of the object.
     *
     * @return string url of the object.
     */
    public function get_category() {
        return $this->category;
    }

    /**
     * Get position of the object.
     *
     * @return integer Position of the object.
     */
    public function get_position() {
        return $this->position;
    }

    /**
     * Get enabled of the object.
     *
     * @return integer enabled of the object.
     */
    public function get_enabled() {
        return $this->enabled;
    }

    /**
     * Get icon of the object.
     *
     * @return string icon of the object.
     */
    public function get_icon() {
        return $this->icon;
    }

    public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        $trace = debug_backtrace();
        trigger_error(
            'Propiedad indefinida mediante __get(): ' . $name .
            ' en ' . $trace[0]['file'] .
            ' en la línea ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    /**
     * Get url to image.
     *
     * @return moodle_url URL or null.
     */
    public function get_img_url() {
        global $DB;
        $context = \context_system::instance();
        $url = null;

        if ($this->img > 0) {
            $file_obj = $DB->get_record('files', array('id' => $this->img));

            $url = \moodle_url::make_pluginfile_url($file_obj->contextid, self::$component, self::$file_area, $file_obj->itemid, $file_obj->filepath, $file_obj->filename);
            return $url;
        }

        return $url;
    }

    /**
     * Get selected roles id for this carousel.
     *
     * @return string comma separated role ids
     */

    public function get_roles() {
        return $this->roles;
    }

    /**
     * Set title of the object.
     *
     * @param string $title New title.
     * @return null
     */
    public function set_title($title = '') {
        $this->title = $title;
        return null;
    }

    /**
     * Set content of the object.
     *
     * @param string $content New content.
     * @return null
     */
    public function set_content($content = '') {
        $this->content = $content;
        return null;
    }

    /**
     * Set alt of the object.
     *
     * @param string $alt New alt.
     * @return null
     */
    public function set_alt($alt = '') {
        $this->alt = $alt;
        return null;
    }

    /**
     * Set url of the object.
     *
     * @param string $url New url.
     * @return null
     */
    public function set_url($url = '') {
        $this->url = $url;
        return null;
    }

    /**
     * Set img of the object.
     *
     * @param string $img New img.
     * @return null
     */
    public function set_img($img = '') {
        $this->img = $img;
        return null;
    }

    /**
     * Set link_target of the object.
     *
     * @param string $link_target New link_target.
     * @return null
     */
    public function set_link_target($link_target = '') {
        $this->link_target = $link_target;
        return null;
    }

    /**
     * Set category of the object.
     *
     * @param string $category New category.
     * @return null or false when we try to set category of existing block
     */
    public function set_category($category = '') {
        // La categoria és modificable
        // if ($this->id > 0) { // category of existing block can't be edited
        //     return false;
        // }

        $category = trim($category);
        $category = strtolower($category);
        $category = preg_replace('/[^A-Za-z0-9]/', '', $category);
        $this->category = $category;
        return null;
    }

    /**
     * Set position of the object.
     *
     * @param int $position New position.
     * @return null
     */
    public function set_position($position = 0) {
        $this->position = $position;
        return null;
    }

    /**
     * Set enabled of the object.
     *
     * @param int $enabled New enabled.
     * @return null
     */
    public function set_enabled($enabled = 0) {
        $this->enabled = $enabled;
        return null;
    }

    /**
     * Set icon of the object.
     *
     * @param string $ico New icon.
     * @return null
     */
    public function set_icon($icon = '') {
        $this->icon = $icon;
        return null;
    }


    /**
     * Set roles for this carousel.
     *
     * @param array.
     * @return null
     */
    public function set_roles($roles = array()) {
        if (!empty($roles)) {
            $saveroleids = array();
            $assignableroles = \block_carousel\manager::get_context_system_roles();

            foreach($roles as $key => $value) {
                // role must exist and must be a system context role
                if ($value == 1) {
                    if (array_key_exists($key, $assignableroles)) {
                        $saveroleids[] = $key;
                    }
                }
            }
            if (!empty($saveroleids)) {
                $this->roles = implode(',', $saveroleids);
            } else {
                $this->roles = '';
            }
        } else {
            $this->roles = '';
        }

        return null;
    }


    /**
     * Delete current image and set img id file. We will move that draft img to final folder.
     *
     * @param int $img Id of the image.
     * @return Bool True or false.
     */
    public function save_img($img = 0) {
        $fs = get_file_storage();
        $context = \context_system::instance();
        $draftitemid = intval($img);
        if ($draftitemid > 0 && $this->id > 0) {
            $this->delete_img();
            file_save_draft_area_files($draftitemid, $context->id, self::$component, self::$file_area, $this->id, array('subdirs' => true));
            $files = $fs->get_area_files($context->id, self::$component, self::$file_area, $this->id, 'sortorder', false);
            $file = reset($files);
            file_set_sortorder($context->id, self::$component, self::$file_area, $this->id, $file->get_filepath(), $file->get_filename(), 1);

            $this->img = $file->get_id();
            return $this->save();
        }
        return true;
    }

    /**
     * Delete image associated to object
     * @return Bool True or false.
     */
    public function delete_img() {
        if ($this->id > 0 && $this->img > 0) {
            $context = \context_system::instance();
            $fs = get_file_storage();
            $fs->delete_area_files($context->id, self::$component, self::$file_area, $this->id);
            $this->img = 0;
            return $this->save();
        }
        return true;
    }

    /**
     * Delete object in database.
     *
     * @return boolean.
     */
    public function delete() {
        global $DB;

        if ($this->id > 0) {
            $current_position = $this->position;
            $current_category = $this->category;
            $this->delete_img();
            $result = $this->delete_object_to_db();

            // -1 position to other items with the same category
            $sql = "UPDATE {".self::$db_table."} SET position = position - 1 WHERE position > :current_position AND category = :current_category";
            $DB->execute($sql, array('current_position' => $current_position, 'current_category' => $current_category));
        }

        $this->__construct();

        return $result;
    }

    public function is_visible_for_user_with_roles($userroles){

        // Si la noticia no té cap rol assigant, tothom la pot veure
        if (empty($this->roles) || $this->roles === '') {
            return true;
        }

        // L'usuari admin pot veure totes les noticies
        if (is_siteadmin()) {
            return true;
        }

        // Si no podem saber el rol que té l'usuari a nivell de sistema, no li mostrem res,
        // Aixó no hauria de passar ni sent guest (!?)
        if (empty($userroles)) {
            return false;
        }

        // Per cada rol assignat que té l'usuari, mirem si és dels rols assignats a la noticia
        // Rols assignats a aquesta notícia
        $carouselroles  = explode(',', $this->roles);

        foreach($userroles as $userrole) {
            if (in_array($userrole->roleid, $carouselroles)){
                return true;
            }
        }

        return false;
    }


}