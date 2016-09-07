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
 * WebGazer block
 *
 * @package    block_webgazer
 * @copyright  Geiser Chalco <geiser@usp.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_heading(
    'configheader', get_string('header_config', 'block_webgazer'),
    get_string('desc_config', 'block_webgazer')));

$settings->add(new admin_setting_configtext(
    'block_webgazer/autosavetime', get_string('autosavetime', 'block_webgazer'),
    get_string('autosavetime_desc', 'block_webgazer'), '0', PARAM_INT));

$settings->add(new admin_setting_configcheckbox(
    'block_webgazer/enablescreenshot', get_string('enablescreenshot', 'block_webgazer'),
    get_string('enablescreenshot_desc', 'block_webgazer'), '0'));

$settings->add(new admin_setting_configcheckbox(
    'block_webgazer/showpredictionpoint', get_string('showpredictionpoint', 'block_webgazer'),
    get_string('showpredictionpoint_desc', 'block_webgazer'), '0'));

$settings->add(new admin_setting_configcheckbox(
    'block_webgazer/showvideocanvas', get_string('showvideocanvas', 'block_webgazer'),
    get_string('showvideocanvas_desc', 'block_webgazer'), '0'));

$tracker_opts = array('clmtrackr' => get_string('clmtrackr', 'block_webgazer'),
                      'trackingjs' => get_string('trackingjs', 'block_webgazer'),
                      'js_objectdetect' => get_string('js_objectdetect', 'block_webgazer'));
$settings->add(new admin_setting_configselect(
    'block_webgazer/tracker', get_string('tracker', 'block_webgazer'),
    get_string('tracker_desc', 'block_webgazer'), 'clmtrackr', $tracker_opts));

$regression_opts = array('ridge' => get_string('ridge', 'block_webgazer'),
                         'weightedRidge' => get_string('weightedRidge', 'block_webgazer'),
                         'threadedRidge' => get_string('threadedRidge', 'block_webgazer'),
                         'linear' => get_string('linear', 'block_webgazer'));
$settings->add(new admin_setting_configselect(
    'block_webgazer/regression', get_string('regression', 'block_webgazer'),
    get_string('regression_desc', 'block_webgazer'), 'ridge', $regression_opts));

