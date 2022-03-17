<?php
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

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('filter_fulltranslate/apikey', get_string('apikey', 'filter_fulltranslate')
        , get_string('apikey_desc', 'filter_fulltranslate'), null, PARAM_ALPHANUM, 15));
    $settings->add(new admin_setting_configcheckbox('filter_fulltranslate/usegoogle', get_string('usegoogle', 'filter_fulltranslate'),
        get_string('usegoogle_desc', 'filter_fulltranslate'), false));
    $settings->add(new admin_setting_configcheckbox('filter_fulltranslate/showstringsinfooter', get_string('showstringsinfooter', 'filter_fulltranslate'),
        get_string('showstringsinfooter_desc', 'filter_fulltranslate'), false));
}