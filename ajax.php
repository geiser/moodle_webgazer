<?php
// This file is based on part of Moodle - http://moodle.org/
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
 * Process ajax requests
 *
 * @copyright Geiser Chalco
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package block_webgazer
 */
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require(__DIR__.'/../../config.php');

$action = required_param('action', PARAM_TEXT);
$sessionid = required_param('sessionid', PARAM_INT);
require_sesskey();

$return = false;

if ($action == "savegazerdata") {
    $gazerdata = $_POST['gazerdata'];
    if ($gazerdata) {
        foreach ($gazerdata as $data) {
            $webgazerlocation = new stdClass();
            $webgazerlocation->sessionid = $sessionid;
            $webgazerlocation->x = $data['x'];
            $webgazerlocation->y = $data['y'];
            $webgazerlocation->elapsedtime = intval($data['time']);
            $DB->insert_record('webgazer_data_location', $webgazerlocation);
        }
        $return = true;
    }
} else if ($action == "savescreenshot") {
    $screenshot = optional_param('screenshot', false, PARAM_RAW);
    if ($screenshot) {
        $DB->set_field('webgazer_session', 'screenshot', $screenshot, array('id'=>$sessionid));
        $return = true;
    }
}

echo json_encode($return);
die;
