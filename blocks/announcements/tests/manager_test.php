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
 * Unit tests manager. Per provar-ho:
 * vendor/bin/phpunit block_announcements_manager_testcase blocks/announcements/tests/manager_test.php
 *
 * @package    local_bsm
 * @category   phpunit
 * @copyright  2015 UPCnet
 */
class block_announcements_manager_testcase extends advanced_testcase {

    /**
     * Set up function. In this instance we are setting up database
     * records to be used in the unit tests.
     */
    protected function setUp() {
        global $DB, $CFG;
        parent::setUp();
    }

    /**
     * Check get_all_categorys function
     */
    public function test_get_all_categorys() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        // Empty database
        $categorys = \block_announcements\manager::get_all_categorys();
        $this->assertEmpty($categorys);

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        // Fill database
        $category = '';
        for ($cont = 0; $cont <= 1; $cont++) {
            $announcement = new \block_announcements\announcement();
            $announcement->set_title($cont);
            $announcement->set_content($cont);
            $announcement->set_url();
            $announcement->set_link_target(reset($keys_link_targets));
            $announcement->set_category($category);
            $announcement->save();
        }
        $categorys = \block_announcements\manager::get_all_categorys();
        $this->assertCount(1, $categorys);

        // Fill database 2
        $category = 'category1';
        for ($cont = 0; $cont <= 1; $cont++) {
            $announcement = new \block_announcements\announcement();
            $announcement->set_title($cont);
            $announcement->set_content($cont);
            $announcement->set_url();
            $announcement->set_link_target(reset($keys_link_targets));
            $announcement->set_category($category);
            $announcement->save();
        }
        $categorys = \block_announcements\manager::get_all_categorys();
        $this->assertCount(2, $categorys);
    }

    /**
     * Check get_last_category_position function
     */
    public function test_get_last_category_position() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        // Empty database
        $position = \block_announcements\manager::get_last_category_position('');
        $this->assertEquals($position, 0);
        $position = \block_announcements\manager::get_last_category_position('category1');
        $this->assertEquals($position, 0);

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        // Fill database
        $category = '';
        for ($cont = 0; $cont <= 1; $cont++) {
            $announcement = new \block_announcements\announcement();
            $announcement->set_title($cont);
            $announcement->set_content($cont);
            $announcement->set_url();
            $announcement->set_link_target(reset($keys_link_targets));
            $announcement->set_category($category);
            $announcement->save();
        }
        $position = \block_announcements\manager::get_last_category_position('');
        $this->assertEquals($position, 1);
    }

    /**
     * Check moving up a block inside the list
     */
    public function test_move_up() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        // Empty database, try to move up something
        for($cont = 0; $cont <= 5; $cont++) {
            $result = \block_announcements\manager::move_block($cont, 0);
            $this->assertNull($result);
        }

        // Fill database
        $this->fill_categorys_block_move_db();

        // Move up block with only 1 position in category
        $blocks = \block_announcements\manager::get_blocks_by_category('');
        $this->assertCount(1, $blocks);
        $block = array_shift($blocks);
        $result = \block_announcements\manager::move_block($block->get_id(), 0);
        $this->assertNull($result);

        // Move up last block
        $last_category_position = \block_announcements\manager::get_last_category_position('category1');
        $blocks = \block_announcements\manager::get_blocks_by_category('category1');
        $block = $blocks[count($blocks) - 1];
        $result = \block_announcements\manager::move_block($block->get_id(), 0);
        $this->assertNotNull($result);
        $this->assertEquals($result, $last_category_position - 1);

        // Move up middle block
        $blocks = \block_announcements\manager::get_blocks_by_category('category1');
        $block = $blocks[count($blocks) - 5];
        $oldposition = $block->get_position();
        $result = \block_announcements\manager::move_block($block->get_id(), 0);
        $this->assertNotNull($result);
        $this->assertEquals($result, $oldposition - 1);

        // Move up first
        $blocks = \block_announcements\manager::get_blocks_by_category('category1');
        $block = array_shift($blocks);
        $result = \block_announcements\manager::move_block($block->get_id(), 0);
        $this->assertNull($result);
    }

    /**
     * Check moving down a block inside the list
     */
    public function test_move_down() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        // Empty database, try to move up something
        for($cont = 0; $cont <= 5; $cont++) {
            $result = \block_announcements\manager::move_block($cont, 1);
            $this->assertNull($result);
        }

        // Fill database
        $this->fill_categorys_block_move_db();

        // Move dow block with only 1 position in category
        $blocks = \block_announcements\manager::get_blocks_by_category('');
        $this->assertCount(1, $blocks);
        $block = array_shift($blocks);
        $result = \block_announcements\manager::move_block($block->get_id(), 1);
        $this->assertNull($result);

        // Move down first
        $blocks = \block_announcements\manager::get_blocks_by_category('category1');
        $block = array_shift($blocks);
        $oldposition = $block->get_position();
        $result = \block_announcements\manager::move_block($block->get_id(), 1);
        $this->assertNotNull($result);
        $this->assertEquals($result, $oldposition + 1);

        // Move down middle block
        $blocks = \block_announcements\manager::get_blocks_by_category('category1');
        $block = $blocks[count($blocks) - 5];
        $oldposition = $block->get_position();
        $result = \block_announcements\manager::move_block($block->get_id(), 1);
        $this->assertNotNull($result);
        $this->assertEquals($result, $oldposition + 1);

        // Move down middle last
        $last_category_position = \block_announcements\manager::get_last_category_position('category1');
        $blocks = \block_announcements\manager::get_blocks_by_category('category1');
        $block = $blocks[count($blocks) - 1];
        $result = \block_announcements\manager::move_block($block->get_id(), 1);
        $this->assertNull($result);
    }

    /**
     * Check get_blocks_by_category function
     */
    public function test_get_blocks_by_category() {
        $this->resetAfterTest(); //aixo fa que la DB quedi com estava un cop acabat

        // Empty database
        $blocks = \block_announcements\manager::get_blocks_by_category('');
        $this->assertEmpty($blocks);
        $blocks = \block_announcements\manager::get_blocks_by_category('category1');
        $this->assertEmpty($blocks);

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);

        // Fill database
        $category = '';
        for ($cont = 0; $cont <= 1; $cont++) {
            $announcement = new \block_announcements\announcement();
            $announcement->set_title($cont);
            $announcement->set_content($cont);
            $announcement->set_url();
            $announcement->set_link_target(reset($keys_link_targets));
            $announcement->set_category($category);
            $announcement->save();
        }
        $category = 'category1';
        for ($cont = 0; $cont <= 1; $cont++) {
            $announcement = new \block_announcements\announcement();
            $announcement->set_title($cont);
            $announcement->set_content($cont);
            $announcement->set_url();
            $announcement->set_link_target(reset($keys_link_targets));
            $announcement->set_category($category);
            $announcement->save();
        }
        $blocks = \block_announcements\manager::get_blocks_by_category('');
        $this->assertCount(2, $blocks);
        foreach ($blocks as $block) {
            $this->assertInstanceOf('\block_announcements\announcement', $block);
        }
    }

    private function fill_categorys_block_move_db() {
        global $DB;

        $link_targets = \block_announcements\announcement::get_values_for_target();
        $keys_link_targets  = array_keys($link_targets);
        $link_target = reset($keys_link_targets);

        $category = '';
        for ($cont = 0; $cont < 1; $cont++) {
            $announcement = new \block_announcements\announcement();
            $announcement->set_title($cont);
            $announcement->set_content($cont);
            $announcement->set_url();
            $announcement->set_link_target($link_target);
            $announcement->set_category($category);
            $announcement->save();
        }
        $this->assertEquals($cont, $DB->count_records(\block_announcements\announcement::$db_table, array('category' => $category)));

        $category = 'category1';
        for ($cont = 0; $cont < 10; $cont++) {
            $announcement = new \block_announcements\announcement();
            $announcement->set_title($category.' '.$cont);
            $announcement->set_content($category.' '.$cont);
            $announcement->set_url();
            $announcement->set_link_target($link_target);
            $announcement->set_category($category);
            $announcement->save();
        }
        $this->assertEquals($cont, $DB->count_records(\block_announcements\announcement::$db_table, array('category' => $category)));
    }

}