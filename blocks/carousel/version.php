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
 * Bootstrap 4 carousel block.
 *
 * @package    block_carousel
 * @copyright  2017 UPCnet
 * @author     Ferran Recio <ferran.recio@upcnet.es>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2020082900;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2016112900;        // Requires this Moodle version
$plugin->component = 'block_carousel'; // Full name of the plugin (used for diagnostics)
