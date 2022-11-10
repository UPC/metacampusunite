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

global $CFG;

/**
 * Unit tests announcement class. Per provar-ho:
 * vendor/bin/phpunit block_announcements_announcement_testcase  blocks/announcements/tests/announcement_test.php
 *
 * @package    block_announcements
 * @category   phpunit
 * @copyright  2017 UPCnet
 */
class block_announcements_announcement_testcase extends advanced_testcase {

    /**
     * Set up function. In this instance we are setting up database
     * records to be used in the unit tests.
     */
    protected function setUp() {
        global $DB, $CFG;
        parent::setUp();
    }

    /**
     * Check constructor
     */
    public function test_construct() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $this->assertEquals(0, $announcement->get_id());
        $this->assertEquals('', $announcement->get_title());
        $this->assertEquals('', $announcement->get_content());
        $this->assertEquals('', $announcement->get_url());
        $this->assertEquals('', $announcement->get_link_target());
        $this->assertEquals('', $announcement->get_category());
        $this->assertEquals('', $announcement->get_icon());
        $this->assertEquals(0, $announcement->get_position());
        $this->assertEquals(1, $announcement->get_enabled());
    }

    /**
     * Check get function
     */
    public function test_get() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        // Empty database
        $announcement = new \block_announcements\announcement();
        $return = $announcement->get(0);
        $this->assertFalse($return);
        $announcement = new \block_announcements\announcement();
        $return = $announcement->get(1);
        $this->assertFalse($return);

        // Insert into DB
        $new_id = $this->insert_record_in_db();
        $announcement = new \block_announcements\announcement();
        $return = $announcement->get($new_id);
        $this->assertTrue($return);
    }

    /**
     * Check get_id function
     */
    public function test_get_id() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $id = $announcement->get_id();
        $this->assertEquals(0, $id);

        // Insert into DB
        $new_id = $this->insert_record_in_db();
        $announcement = new \block_announcements\announcement();
        $return = $announcement->get($new_id);
        $this->assertTrue($return);
        $this->assertEquals($new_id, $announcement->get_id());
    }

    /**
     * Check set_title and get_title function
     */
    public function test_set_get_title() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $title = $announcement->get_title();
        $this->assertEquals('', $title);

        $announcement = new \block_announcements\announcement();
        $announcement->set_title('title');
        $this->assertEquals('title', $announcement->get_title());
    }

    /**
     * Check set_content and get_content function
     */
    public function test_set_get_content() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $content = $announcement->get_content();
        $this->assertEquals('', $content);

        $announcement = new \block_announcements\announcement();
        $announcement->set_content('content');
        $this->assertEquals('content', $announcement->get_content());
    }

    /**
     * Check set_url and get_url function
     */
    public function test_set_get_url() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $this->assertEquals('', $announcement->get_url());

        $announcement = new \block_announcements\announcement();
        $announcement->set_url('http://www.upcnet.es');
        $this->assertEquals('http://www.upcnet.es', $announcement->get_url());
    }

    /**
     * Check set_link_target and get_link_target function
     */
    public function test_set_get_link_target() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $this->assertEquals('', $announcement->get_link_target());

        $announcement = new \block_announcements\announcement();
        $announcement->set_link_target('_blank');
        $this->assertEquals('_blank', $announcement->get_link_target());
    }

    /**
     * Check set_category and get_category function
     */
    public function test_set_get_category() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $this->assertEquals('', $announcement->get_category());

        $announcement = new \block_announcements\announcement();
        $announcement->set_category('category1');
        $this->assertEquals('category1', $announcement->get_category());

        $announcement = new \block_announcements\announcement();
        $announcement->set_category(' category1 ');
        $this->assertEquals('category1', $announcement->get_category());

        $announcement = new \block_announcements\announcement();
        $announcement->set_category('category1');
        $this->assertEquals('category1', $announcement->get_category());

        // category can't be edited in a already created announcement
        $new_id = $this->insert_record_in_db();
        $announcement = new \block_announcements\announcement();
        $return = $announcement->get($new_id);
        $this->assertTrue($return);
        $this->assertFalse($announcement->set_category('category1'));
    }

    /**
     * Check clean category on set
     */
    public function test_category_clean() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $category1 = 'category1';
        $category1_ok = 'category1';
        $category2 = 'category2 ';
        $category2_ok = 'category2';
        $category3 = 'category1,category2';
        $category3_ok = 'category1category2';

        $announcement = new \block_announcements\announcement();
        $announcement->set_category($category1);
        $this->assertEquals($announcement->get_category(), $category1_ok);
        $announcement = new \block_announcements\announcement();
        $announcement->set_category($category2);
        $this->assertEquals($announcement->get_category(), $category2_ok);
        $announcement = new \block_announcements\announcement();
        $announcement->set_category($category3);
        $this->assertEquals($announcement->get_category(), $category3_ok);
    }

    /**
     * Check set_position and get_position function
     */
    public function test_set_get_position() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $this->assertEquals(0, $announcement->get_position());

        // Insert into DB
        $announcement = new \block_announcements\announcement();
        $announcement->set_position(1);
        $this->assertEquals(1, $announcement->get_position());
    }

    /**
     * Check set_enabled and get_enabled function
     */
    public function test_set_get_enabled() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $this->assertEquals(1, $announcement->get_enabled());

        // Insert into DB
        $announcement = new \block_announcements\announcement();
        $announcement->set_enabled(1);
        $this->assertEquals(1, $announcement->get_enabled());
    }

    /**
     * Check set_icon and get_icon function
     */
    public function test_set_get_icon() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $announcement = new \block_announcements\announcement();
        $this->assertEquals('', $announcement->get_icon());

        $announcement = new \block_announcements\announcement();
        $announcement->set_icon('star');
        $this->assertEquals('star', $announcement->get_icon());
    }

    /**
     * Check validation fields
     */
    public function test_validation_fields() {
        global $DB;
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        $title_ok = 'title';
        $content_ok = 'title';
        $url_ok = 'http://www.url.com';
        $category_ok = 'category1';
        $icon_ok = 'star';
        $position_ok = 0;
        $enabled_ok = 1;
        $link_target_ok = reset($keys_link_targets);

        // Validate title
        $announcement = new \block_announcements\announcement();
        $announcement->set_title(null);
        $announcement->set_content($content_ok);
        $announcement->set_url($url_ok);
        $announcement->set_link_target($link_target_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_category($category_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);

        $errors = $announcement->validate();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('title', $errors);
        // End validate title

        // Validate URL
        $announcement = new \block_announcements\announcement();
        $announcement->set_title($title_ok);
        $announcement->set_content($content_ok);
        $announcement->set_url('not valid URL');
        $announcement->set_link_target($link_target_ok);
        $announcement->set_category($category_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);

        $errors = $announcement->validate();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('url', $errors);
        // End validate URL

        //Validate link target
        $announcement = new \block_announcements\announcement();
        $announcement->set_title($title_ok);
        $announcement->set_content($content_ok);
        $announcement->set_url($url_ok);
        $announcement->set_link_target('blablabla');
        $announcement->set_category($category_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);
        $errors = $announcement->validate();

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('link_target', $errors);
        // End validatelink target
    }

    /**
     * Check save function
     */
    public function test_save_ko() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        $title_ok = 'title';
        $content_ok = 'title';
        $url_ok = 'http://www.url.com';
        $category_ok = 'category1';
        $position_ok = 0;
        $enabled_ok = 1;
        $icon_ok = 'star';
        $link_target_ok = reset($keys_link_targets);

        // Title
        $announcement = new \block_announcements\announcement();
        $announcement->set_title('');
        $announcement->set_content($content_ok);
        $announcement->set_url($url_ok);
        $announcement->set_link_target($link_target_ok);
        $announcement->set_category($category_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);
        $result = $announcement->save();
        $this->assertFalse($result, 'save_ko num: 1');

        // Url
        $announcement = new \block_announcements\announcement();
        $announcement->set_title($title_ok);
        $announcement->set_content($content_ok);
        $announcement->set_url('URLKO');
        $announcement->set_link_target($link_target_ok);
        $announcement->set_category($category_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);

        $result = $announcement->save();
        $this->assertFalse($result);

        // Link target
        $announcement = new \block_announcements\announcement();
        $announcement->set_title($title_ok);
        $announcement->set_content($content_ok);
        $announcement->set_url($url_ok);
        $announcement->set_link_target('blablabla');
        $announcement->set_category($category_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);

        $result = $announcement->save();
        $this->assertFalse($result);
    }

    /**
     * Check save function
     */
    public function test_save_ok() {
        global $DB;
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        $title = 'title';
        $content = 'title';
        $url = 'http://www.url.com';
        $category = 'category1';
        $position = 0;
        $enabled = 1;
        $icon = 'star';
        $link_target = reset($keys_link_targets);

        $announcement = new \block_announcements\announcement();
        $announcement->set_title($title);
        $announcement->set_content($content);
        $announcement->set_url($url);
        $announcement->set_link_target($link_target);
        $announcement->set_category($category);
        $announcement->set_icon($icon);
        $announcement->set_position($position);
        $announcement->set_enabled($enabled);
        $announcement->save();
        $this->assertGreaterThan(0, $announcement->get_id());
        $this->assertEquals($title, $DB->get_field(\block_announcements\announcement::$db_table, 'title', array('id' => $announcement->get_id())));
        $this->assertEquals($content, $DB->get_field(\block_announcements\announcement::$db_table, 'content', array('id' => $announcement->get_id())));
        $this->assertEquals($url, $DB->get_field(\block_announcements\announcement::$db_table, 'url', array('id' => $announcement->get_id())));
        $this->assertEquals($link_target, $DB->get_field(\block_announcements\announcement::$db_table, 'link_target', array('id' => $announcement->get_id())));
        $this->assertEquals($category, $DB->get_field(\block_announcements\announcement::$db_table, 'category', array('id' => $announcement->get_id())));
        $this->assertEquals($position, $DB->get_field(\block_announcements\announcement::$db_table, 'position', array('id' => $announcement->get_id())));
        $this->assertEquals($enabled, $DB->get_field(\block_announcements\announcement::$db_table, 'enabled', array('id' => $announcement->get_id())));
        $this->assertEquals($icon, $DB->get_field(\block_announcements\announcement::$db_table, 'icon', array('id' => $announcement->get_id())));
    }

    /**
     * Check save function
     */
    public function test_edit_ko() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        $title_ok = 'title';
        $content_ok = 'title';
        $url_ok = 'http://www.url.com';
        $category_ok = 'category1';
        $position_ok = 0;
        $enabled_ok = 1;
        $icon_ok = 'star';
        $link_target_ok = reset($keys_link_targets);

        // Insert into DB
        $new_id = $this->insert_record_in_db();

        // Title
        $announcement = new \block_announcements\announcement();
        $result_get = $announcement->get($new_id);
        $this->assertTrue($result_get);
        $announcement->set_title('');
        $announcement->set_content($content_ok);
        $announcement->set_url($url_ok);
        $announcement->set_link_target($link_target_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_category($category_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);
        $result = $announcement->save();
        $this->assertFalse($result);

        // Url
        $announcement = new \block_announcements\announcement();
        $result_get = $announcement->get($new_id);
        $this->assertTrue($result_get);
        $announcement->set_title($title_ok);
        $announcement->set_content($content_ok);
        $announcement->set_url('ko');
        $announcement->set_link_target($link_target_ok);
        $announcement->set_icon($icon_ok);
        $announcement->set_category($category_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);
        $result = $announcement->save();
        $this->assertFalse($result);

        // Link target
        $announcement = new \block_announcements\announcement();
        $result_get = $announcement->get($new_id);
        $this->assertTrue($result_get);
        $announcement->set_title($title_ok);
        $announcement->set_content($content_ok);
        $announcement->set_url($url_ok);
        $announcement->set_link_target('blablabla');
        $announcement->set_icon($icon_ok);
        $announcement->set_category($category_ok);
        $announcement->set_position($position_ok);
        $announcement->set_enabled($enabled_ok);
        $result = $announcement->save();
        $this->assertFalse($result);
    }

    /**
     * Check save function
     */
    public function test_edit_ok() {
        global $DB;
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        $title = 'title';
        $content = 'title';
        $url = 'http://www.url.com';
        $category = 'category1';
        $position = 0;
        $enabled = 1;
        $icon = 'star';
        $link_target = reset($keys_link_targets);

        // Insert into DB
        $new_id = $this->insert_record_in_db();

        $announcement = new \block_announcements\announcement();
        $result_get = $announcement->get($new_id);
        $this->assertTrue($result_get);
        $announcement->set_title($title);
        $announcement->set_content($content);
        $announcement->set_url($url);
        $announcement->set_link_target($link_target);
        $announcement->set_category($category);
        $announcement->set_icon($icon);
        $announcement->set_position($position);
        $announcement->set_enabled($enabled);
        $announcement->save();
        $this->assertEquals($new_id, $announcement->get_id());
        $this->assertEquals($title, $DB->get_field(\block_announcements\announcement::$db_table, 'title', array('id' => $announcement->get_id())));
        $this->assertEquals($content, $DB->get_field(\block_announcements\announcement::$db_table, 'content', array('id' => $announcement->get_id())));
        $this->assertEquals($url, $DB->get_field(\block_announcements\announcement::$db_table, 'url', array('id' => $announcement->get_id())));
        $this->assertEquals($link_target, $DB->get_field(\block_announcements\announcement::$db_table, 'link_target', array('id' => $announcement->get_id())));
        $this->assertEquals($category, $DB->get_field(\block_announcements\announcement::$db_table, 'category', array('id' => $announcement->get_id())));
        $this->assertEquals($position, $DB->get_field(\block_announcements\announcement::$db_table, 'position', array('id' => $announcement->get_id())));
        $this->assertEquals($enabled, $DB->get_field(\block_announcements\announcement::$db_table, 'enabled', array('id' => $announcement->get_id())));
        $this->assertEquals($icon, $DB->get_field(\block_announcements\announcement::$db_table, 'icon', array('id' => $announcement->get_id())));
    }

    /**
     * Check delete function
     */
    public function test_delete() {
        global $DB;
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        // Init data
        $new_object = new stdClass();
        $new_object->title  = 'title';
        $new_object->content  = 'content';
        $new_object->url  = 'http://www.upcnet.es';
        $new_object->link_target  = '_blank';
        $new_object->category  = 'category1';
        $new_object->img  = 0;
        $new_object->position  = 0;
        $new_object->enabled  = 1;
        $new_object->timecreated  = time();
        $new_object->timemodified  = time();
        $new_id = $DB->insert_record(\block_announcements\announcement::$db_table, $new_object);

        $new_object = new stdClass();
        $new_object->title  = 'title2';
        $new_object->content  = 'content2';
        $new_object->url  = 'http://www.upcnet.es';
        $new_object->link_target  = '_blank';
        $new_object->category  = 'category1';
        $new_object->img  = 0;
        $new_object->position  = 1;
        $new_object->enabled  = 1;
        $new_object->timecreated  = time();
        $new_object->timemodified  = time();
        $new_id2 = $DB->insert_record(\block_announcements\announcement::$db_table, $new_object);

        $announcement = new \block_announcements\announcement();
        $result_get = $announcement->get($new_id);
        $this->assertTrue($result_get);
        $result_delete = $announcement->delete();
        $this->assertTrue($result_delete);
        $this->assertEquals(0, $announcement->get_id());

        $announcement = new \block_announcements\announcement();
        $result_get = $announcement->get($new_id2);
        $this->assertTrue($result_get);
        $this->assertEquals(0, $announcement->get_position());
    }

    /**
     * Check save_img and delete_img function
     */
    public function test_save_delete_img() {
        global $DB, $USER;

        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat
        $this->setAdminUser(); // Must be a non-guest user to create resources.

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        $title = 'title';
        $content = 'title';
        $url = 'http://www.url.com';
        $category = 'category1';
        $position = 0;
        $enabled = 1;
        $icon = 'star';
        $link_target = reset($keys_link_targets);

        // Add file to moodle
        $itemid = file_get_unused_draft_itemid();
        $usercontext = context_user::instance($USER->id);
        $filerecord = array('component' => 'user', 'filearea' => 'draft', 'contextid' => $usercontext->id, 'itemid' => $itemid, 'filename' => rand(0,100).time().'.txt', 'filepath' => '/');
        $fs = get_file_storage();
        $file_object = $fs->create_file_from_string($filerecord, 'Test save_img '.microtime().' file');
        // End add file to moodle

        $announcement = new \block_announcements\announcement();
        $announcement->set_title($title);
        $announcement->set_content($content);
        $announcement->set_url($url);
        $announcement->set_link_target($link_target);
        $announcement->set_category($category);
        $announcement->set_icon($icon);
        $announcement->set_position($position);
        $announcement->set_enabled($enabled);
        $this->assertTrue($announcement->save());

        $result = $announcement->save_img($itemid);
        $this->assertTrue($result);
        $this->assertInstanceOf('moodle_url', $announcement->get_img_url());

        $context = context_system::instance();
        $files = $fs->get_area_files($context->id, \block_announcements\announcement::$component, \block_announcements\announcement::$file_area, $announcement->get_id(), "itemid, filepath, filename", false);
        $this->assertCount(1, $files);

        // Test delete
        $result = $announcement->delete_img();
        $this->assertTrue($result);
        $this->assertNull($announcement->get_img_url());

        $context = context_system::instance();
        $files = $fs->get_area_files($context->id, \block_announcements\announcement::$component, \block_announcements\announcement::$file_area, $announcement->get_id(), "itemid, filepath, filename", false);
        $this->assertEmpty($files);
    }

    /**
     * Insert record into database
     * @return new_id
     */
    private function insert_record_in_db() {
        global $DB;

        // Insert into DB
        $new_object = new stdClass();
        $new_object->title  = 'title';
        $new_object->content  = 'content';
        $new_object->url  = 'http://www.upcnet.es';
        $new_object->link_target = '_blank';
        $new_object->category  = 'category1';
        $new_object->img  = 0;
        $new_object->position  = 0;
        $new_object->enabled  = 1;
        $new_object->icon = '';
        $new_object->timecreated  = time();
        $new_object->timemodified  = time();
        $new_id = $DB->insert_record(\block_announcements\announcement::$db_table, $new_object);
        return $new_id;
    }
}