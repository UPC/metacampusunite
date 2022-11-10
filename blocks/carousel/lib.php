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
 * Form for editing HTML block instances.
 *
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   block_carousel
 * @category  files
 * @param stdClass $course course object
 * @param stdClass $birecord_or_cm block instance record
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 * @todo MDL-36050 improve capability check on stick blocks, so we can check user capability before sending images.
 */
function block_carousel_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG, $USER;

    // If block is in course context, then check if user has capability to access course.
    if ($CFG->forcelogin) {
        require_login();
    }

    $fs = get_file_storage();

    $filename = array_pop($args);
    $itemid = reset($args);


    if (!$file = $fs->get_file($context->id, 'block_carousel', $filearea, $itemid, '/', $filename) or $file->is_directory()) {
        send_file_not_found();
    }

/* //#MODIFIED# remove if not used
    if ($parentcontext = context::instance_by_id($birecord_or_cm->parentcontextid, IGNORE_MISSING)) {
        if ($parentcontext->contextlevel == CONTEXT_USER) {
            // force download on all personal pages including /my/
            //because we do not have reliable way to find out from where this is used
            $forcedownload = true;
        }
    } else {
        // weird, there should be parent context, better force dowload then
        $forcedownload = true;
    }
*/


    // NOTE: it woudl be nice to have file revisions here, for now rely on standard file lifetime,
    //       do not lower it because the files are dispalyed very often.
    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Perform global search replace such as when migrating site to new URL.
 * @param  $search
 * @param  $replace
 * @return void
 */
function block_carousel_global_db_replace($search, $replace) {
    global $DB;

    $instances = $DB->get_recordset('block_instances', array('blockname' => 'carousel'));
    foreach ($instances as $instance) {
        // TODO: intentionally hardcoded until MDL-26800 is fixed
        $config = unserialize(base64_decode($instance->configdata));
        if (isset($config->text) and is_string($config->text)) {
            $config->text = str_replace($search, $replace, $config->text);
            $DB->set_field('block_instances', 'configdata', base64_encode(serialize($config)), array('id' => $instance->id));
        }
    }
    $instances->close();
}


function block_carousel_get_images($filter_category = '', $roles = null) {
    global $DB;

    $images = $DB->get_records('block_carousel', array('category'=>$filter_category), 'position ASC');
    $filteredimages = [];
    foreach ($images as $image) {
        $carousel = new \block_carousel\carousel();
        $carousel->get($image->id);
        $image->imgsrc = $carousel->get_img_url() ?: null;

		if ($roles !== null) {
			//Filter by role
			$roleids = array_filter(explode(',',$carousel->get_roles()));
			if ($roles) {
				if ($roleids && !array_intersect($roles, $roleids)) {
					continue;
				}
			} elseif ($roleids) {
				continue;
			}
		}

        $image->roles = block_carousel_get_formatted_role_names($carousel->get_roles());
        $filteredimages[] = $image;
    }

    return $filteredimages;
}

/**
 * @param $roles string Role ids separated by comma
 * @return array Role names
 */
function block_carousel_get_formatted_role_names($roles) {
    $rolenames = [];
    if (!empty($roles)) {
        $rolesids = explode(',',$roles);
        $assignableroles = block_carousel_get_context_system_roles();
        foreach ($rolesids as $roleid) {
            $rolenames[] = $assignableroles[$roleid];
        }
    }
    return $rolenames;
}

function block_carousel_get_context_system_roles(){
    global $DB;

    $context = \context_system::instance();
    $params = array('contextlevel' => $context->contextlevel);
    

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0; // no course aliases
        $coursecontext = null;
    }

    $sql = "SELECT r.id, r.name, r.shortname, rn.name AS coursealias
        FROM {role} r
        JOIN {role_context_levels} rcl ON (rcl.contextlevel = :contextlevel AND r.id = rcl.roleid)
        LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
        ORDER BY r.sortorder ASC";
    $roles = $DB->get_records_sql($sql, $params);

    $rolenames = role_fix_names($roles, $coursecontext, ROLENAME_ORIGINAL, true);

    return $rolenames;
}

function block_carousel_get_category_sigle_select($filtercategory='') {
    global $OUTPUT;

    $all_categories = \block_carousel\manager::get_all_categories();
    $choices = array();

    $choices[''] = get_string('no_category', 'block_carousel');
    foreach ($all_categories as $category) {
        if ($category == '') continue;
        $choices[$category] = $category;
    }
    $categoryfilterformurl = new moodle_url('/blocks/carousel/manager/index.php', array('filter_category' => $filtercategory));

    $html = '<div class="carousel-manager-category-selector row">';
    $html .= '<div class="carousel-manager-category-selector-label p-2 mr-3">'.get_string('category').'</div>';
    $html .= $OUTPUT->single_select($categoryfilterformurl, 'filter_category', $choices, $filtercategory, [], 'filter_category');
    $html .= '</div>';

    return $html;
}
