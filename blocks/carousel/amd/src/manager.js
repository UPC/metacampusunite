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
            // Click on up arrow
            $("a[id^='carousel_up_']").click(function() {
                var aux_arr = this.id.split('carousel_up_');
                var carousel_id = parseInt(aux_arr[1]);
                var url = config.wwwroot + '/blocks/carousel/js/ajax/ajax_up_carousel.php';

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: url+'?id='+carousel_id,
                    async: false,
                    success: function(response) {
                        // nothing todo with response
                        $('#filter_category').submit(); // submit form
                    }
                });
            });

            // Click on down arrow
            $("a[id^='carousel_down_']").click(function() {
                var aux_arr = this.id.split('carousel_down_');
                var carousel_id = parseInt(aux_arr[1]);
                var url = config.wwwroot + '/blocks/carousel/js/ajax/ajax_down_carousel.php';

                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: url+'?id='+carousel_id,
                    async: false,
                    success: function(response) {
                        // nothing todo with response
                        $('#filter_category').submit(); // submit form
                    }
                });
            });

            $("a[id^='carousel_up_']").first().css({"color":'grey'}).off();
            $("a[id^='carousel_down_']").last().css({"color":'grey'}).off();
        }
    };
});