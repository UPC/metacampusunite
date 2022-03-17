<?php

/**
 * Moodleoverflow posts search area
 *
 * @package    mod_moodleoverflow
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_moodleoverflow\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forum/lib.php'); //#TODO# is it necessary?

/**
 * Moodleoverflow posts search area.
 *
 * @package    mod_moodleoverflow
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post extends \core_search\base_mod {

    /**
     * @var array Internal quick static cache.
     */
    protected $moodleoverflowsdata = array();

    /**
     * @var array Internal quick static cache.
     */
    protected $discussionsdata = array();

    /**
     * @var array Internal quick static cache.
     */
    protected $postsdata = array();

    /**
     * Returns recordset containing required data for indexing moodleoverflow posts.
     *
     * @param int $modifiedfrom timestamp
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;

        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql(
                $context, 'moodleoverflow', 'mo');
        if ($contextjoin === null) {
            return null;
        }

        // $sql = "SELECT mop.*, mo.id AS moodleoverflowid, mo.course AS courseid, mod.groupid AS groupid //#TODO# groupid removed
        $sql = "SELECT mop.*, mo.id AS moodleoverflowid, mo.course AS courseid, mod.name AS discussionname
                  FROM {moodleoverflow_posts} mop
                  JOIN {moodleoverflow_discussions} mod ON mod.id = mop.discussion
                  JOIN {moodleoverflow} mo ON mo.id = mod.moodleoverflow
          $contextjoin
                 WHERE mop.modified >= ? ORDER BY mop.modified ASC";
        return $DB->get_recordset_sql($sql, array_merge($contextparams, [$modifiedfrom]));
    }

    /**
     * Returns the document associated with this post id.
     *
     * @param stdClass $record Post info.
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {

        try {
            $cm = $this->get_cm('moodleoverflow', $record->moodleoverflowid, $record->courseid);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->discussionname, false));
        $doc->set('content', content_to_text($record->message, $record->messageformat));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $record->courseid);
        $doc->set('userid', $record->userid);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->modified);

		/* //#TODO# groupid not used in mooodleoverflow discussions
        // Store group id if there is one. (0 and -1 both mean not restricted to group.)
        if ($record->groupid > 0) {
            $doc->set('groupid', $record->groupid);
        }
		*/

		/* //#TODO# pending to decide its content
		// Extra contents associated to the document.
		$doc->set('description1', content_to_text($record->extracontent1, $record->extracontent1format));
		$doc->set('description2', content_to_text($record->extracontent2, $record->extracontent2format));
		*/

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $record->created)) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Return the context info required to index files for
     * this search area.
     *
     * @return array
     */
    public function get_search_fileareas() {
        $fileareas = array(
            'attachment',
            'post'
        );

        return $fileareas;
    }

    /**
     * Add the moodleoverflow post attachments.
     *
     * @param document $document The current document
     * @return null
     */
    public function attach_files($document) {
        global $DB;

        $postid = $document->get('itemid');

        try {
            $post = $this->get_post($postid);
        } catch (\dml_missing_record_exception $e) {
            unset($this->postsdata[$postid]);
            debugging('Could not get record to attach files to '.$document->get('id'), DEBUG_DEVELOPER);
            return;
        }

        // Because this is used during indexing, we don't want to cache posts. Would result in memory leak.
        unset($this->postsdata[$postid]);

        $cm = $this->get_cm($this->get_module_name(), $post->moodleoverflow, $document->get('courseid'));
        $context = \context_module::instance($cm->id);
        $contextid = $context->id;

        $fileareas = $this->get_search_fileareas();
        $component = $this->get_component_name();

        // Get the files and attach them.
        foreach ($fileareas as $filearea) {
            $fs = get_file_storage();
            $files = $fs->get_area_files($contextid, $component, $filearea, $postid, '', false);

            foreach ($files as $file) {
                $document->add_stored_file($file);
            }
        }
    }

    /**
     * Whether the user can access the document or not.
     *
     * @throws \dml_missing_record_exception
     * @throws \dml_exception
     * @param int $id Moodleoverflow post id
     * @return bool
     */
    public function check_access($id) {
        global $USER;

        try {
            $post = $this->get_post($id);
            $moodleoverflow = $this->get_moodleoverflow($post->moodleoverflow);
            $discussion = $this->get_discussion($post->discussion);
            $cminfo = $this->get_cm('moodleoverflow', $moodleoverflow->id, $moodleoverflow->course);
            $cm = $cminfo->get_course_module_record();
        } catch (\dml_missing_record_exception $ex) {
            return \core_search\manager::ACCESS_DELETED;
        } catch (\dml_exception $ex) {
            return \core_search\manager::ACCESS_DENIED;
        }

        // Recheck uservisible although it should have already been checked in core_search.
        if ($cminfo->uservisible === false) {
            return \core_search\manager::ACCESS_DENIED;
        }

        if (!forum_user_can_see_post($moodleoverflow, $discussion, $post, $USER, $cm)) {
            return \core_search\manager::ACCESS_DENIED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Link to the moodleoverflow post discussion
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        // The post is already in static cache, we fetch it in self::search_access.
		$post = $this->get_post($doc->get('itemid'));
		$permalink = new \moodle_url('/mod/moodleoverflow/discussion.php', array('d' => $post->discussion));
		$permalink->set_anchor('p' . $post->id);
        return $permalink;
    }

    /**
     * Link to the moodleoverflow.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $contextmodule = \context::instance_by_id($doc->get('contextid'));
        return new \moodle_url('/mod/moodleoverflow/view.php', array('id' => $contextmodule->instanceid));
    }

    /**
     * Returns the specified moodleoverflow post from its internal cache.
     *
     * @throws \dml_missing_record_exception
     * @param int $postid
     * @return stdClass
     */
    protected function get_post($postid) {
        if (empty($this->postsdata[$postid])) {
            $this->postsdata[$postid] = $this->moodleoverflow_get_post_full($postid);
            if (!$this->postsdata[$postid]) {
                throw new \dml_missing_record_exception('mooodleoverflow_posts');
            }
        }
        return $this->postsdata[$postid];
    }

    /**
     * Returns the specified moodleoverflow checking the internal cache.
     *
     * Store minimal information as this might grow.
     *
     * @throws \dml_exception
     * @param int $moodleoverflowid
     * @return stdClass
     */
    protected function get_moodleoverflow($moodleoverflowid) {
        global $DB;

        if (empty($this->moodleoverflowdata[$moodleoverflowid])) {
            $this->moodleoverflowdata[$moodleoverflowid] = $DB->get_record('moodleoverflow', array('id' => $moodleoverflowid), '*', MUST_EXIST);
        }
        return $this->moodleoverflowdata[$moodleoverflowid];
    }

    /**
     * Returns the discussion checking the internal cache.
     *
     * @throws \dml_missing_record_exception
     * @param int $discussionid
     * @return stdClass
     */
    protected function get_discussion($discussionid) {
        global $DB;

        if (empty($this->discussionsdata[$discussionid])) {
            $this->discussionsdata[$discussionid] = $DB->get_record('moodleoverflow_discussions',
                array('id' => $discussionid), '*', MUST_EXIST);
        }
        return $this->discussionsdata[$discussionid];
    }

    /**
     * Changes the context ordering so that the forums with most recent discussions are indexed
     * first.
     *
     * @return string[] SQL join and ORDER BY
     */
    protected function get_contexts_to_reindex_extra_sql() {
        return [
            'JOIN {moodleoverflow_discussions} fd ON mod.course = cm.course AND mod.moodleoverflow = cm.instance',
            'MAX(mod.timemodified) DESC'
        ];
    }

    /**
     * Confirms that data entries support group restrictions.
     *
     * @return bool True
     */
    public function supports_group_restriction() {
        // return true; //#TODO# changed by false
        return false;
    }


	/**
	 * Gets a post with all info ready for forum_print_post
	 * Most of these joins are just to get the forum id
	 *
	 * @global object
	 * @global object
	 * @param int $postid
	 * @return mixed array of posts or false
	 */
	protected function moodleoverflow_get_post_full($postid) {
		global $CFG, $DB;

		$allnames = get_all_user_name_fields(true, 'u');
		return $DB->get_record_sql("SELECT p.*, d.moodleoverflow, $allnames, u.email, u.picture, u.imagealt
								 FROM {moodleoverflow_posts} p
									  JOIN {moodleoverflow_discussions} d ON p.discussion = d.id
									  LEFT JOIN {user} u ON p.userid = u.id
								WHERE p.id = ?", array($postid));
	}
}
