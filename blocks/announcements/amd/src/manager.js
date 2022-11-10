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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * manager block_announcements.
 *
 * @module     block_announcements/manager
 * @copyright  2017 UPCNet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/config'], function($, config) {
    return {
        init: function() {
            // Onchange category selector, send form
            $('#filter_category').change(function() {
                window.onbeforeunload = null;
                $('#category_filter_form').submit();
            });

            // Click on up arrow
            $("a[id^='announcement_up_']").click(function() {
                var aux_arr = this.id.split('announcement_up_');
                var announcement_id = parseInt(aux_arr[1]);
                var url = config.wwwroot + '/blocks/announcements/js/ajax/ajax_up_announcement.php';

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: url+'?id='+announcement_id,
                    async: false,
                    success: function(response) {
                        // nothing todo with response
                        $('#category_filter_form').submit(); // submit form
                    }
                });
            });

            // Click on down arrow
            $("a[id^='announcement_down_']").click(function() {
                var aux_arr = this.id.split('announcement_down_');
                var announcement_id = parseInt(aux_arr[1]);
                var url = config.wwwroot + '/blocks/announcements/js/ajax/ajax_down_announcement.php';

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: url+'?id='+announcement_id,
                    async: false,
                    success: function(response) {
                        // nothing todo with response
                        $('#category_filter_form').submit(); // submit form
                    }
                });
            });
        }
    };
});