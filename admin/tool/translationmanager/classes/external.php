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


defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

class tool_translationmanager_external extends external_api {
    public static function update_data_parameters() {
        return new external_function_parameters(
            array(
                'jsonformdata' => new external_value(PARAM_NOTAGS, 'jsonformdata', VALUE_DEFAULT, 0)
            )
        );
    }
    public static function update_data($jsonformdata) {
        global $DB;
        $data = [];
        parse_str($jsonformdata, $data);
        $record = $DB->get_record('filter_fulltranslate', ['id' => $data['id']]);
        if ($record->textformat == 'plain') {
            $record->translation = $data['translation'];
        } else {
            $record->translation = $data['translation']['text'];
        }
        $record->automatic = 0;
        $record->timemodified = time();
        return ['success' => $DB->update_record('filter_fulltranslate', $record)];
    }
    public static function update_data_returns() {
        return new external_function_parameters(
            array(
                'success' => new external_value(PARAM_BOOL, 'success', VALUE_DEFAULT, false)
            )
        );
    }
}