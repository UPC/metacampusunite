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

/**
 * @package    auth_redirecttosignupform
 * @copyright  2021 Yerai Rodríguez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/auth/redirecttosignupform/signup_form.php');
require_once($CFG->dirroot . '/auth/email/auth.php');

class auth_plugin_redirecttosignupform extends auth_plugin_base {

    public function __construct() {
        $this->authtype = 'redirecttosignupform';
    }

    public function user_login() {
        return false;
    }

    public function is_internal() {
        return true;
    }

    public function can_signup() {
        return true;
    }

    public function signup_form() {
        return new redirecttosignupform_signup_form();
    }

    public function user_signup($user, $notify = true) {
        $user->auth = 'email';
        $auth_email = new auth_plugin_email();
        return $auth_email->user_signup($user, $notify);
    }

    public function can_confirm() {
        return true;
    }

    public function user_confirm($username, $confirmsecret) {
        $auth_email = new auth_plugin_email();
        return $auth_email->user_confirm($username, $confirmsecret);
    }

}


