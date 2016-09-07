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
 * Strings for component 'block_webgazer', language 'en'
 *
 * @package   block_webgazer
 * @copyright Daniel Neis <danielneis@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'WebGazer';
$string['webgazer:addinstance'] = 'Add a webgazer block';
$string['webgazer:myaddinstance'] = 'Add a webgazer block to my moodle';

$string['header_config'] = 'WebGazer Block Config';
$string['desc_config'] = 'Config section for webgazer block';

$string['autosavetime'] = 'Autosave time';
$string['autosavetime_desc'] = 'Time in seconds to save the collected gaze location in the DB';
$string['autosavetime_help'] = 'Time in seconds to save the collected gaze location in the DB. 0 value means that the collected gaze location is only save when the user makes blur or change the current page';

$string['enablescreenshot'] = 'Enable screenshot';
$string['enablescreenshot_desc'] = 'Enable function to take screenshot for each session';
$string['enablescreenshot_help'] = 'Enable function to take screenshot as image when the webgazer is initialized, so that it will be stored in the DB as a BLOB images using html2canvas';

$string['showpredictionpoint'] = 'Show prediction point';
$string['showpredictionpoint_desc'] = 'Shows a circle with the current tracking prediction point';
$string['showpredictionpoint_help'] = 'Shows a red circle with the current tracking prediction point based on the tracker model and regresion model';

$string['showvideocanvas'] = 'Show a video canvas';
$string['showvideocanvas_desc'] = 'Show a video canvas attached to the WebGazer blocks';
$string['showvideocanvas_help'] = 'Show a video canvas attached to the WebGazer blocks';

$string['tracker'] = 'Tracker library';
$string['tracker_desc'] = 'Set the tracker library for facial recognition in which the webgazer should based on';
$string['tracker_help'] = 'Set the tracker library for facial recognition in which the webgazer should based on';

$string['regression'] = 'Regression library';
$string['regression_desc'] = 'Set the regression library in which the webgazer should based on';
$string['regression_help'] = 'Set the regression library in which the webgazer should based on';

$string['clmtrackr'] = 'Facial tracking via constrained local models';
$string['js_objectdetect'] = 'Facial tracking via javascript real-time object detection ';
$string['trackingjs'] = 'Facial tracking via computer vision algorithms';

$string['ridge'] = 'Simple ridge regression model';
$string['weightedRidge'] = 'Weight ridge regression model';
$string['threadedRidge'] = 'Thread-based ridge regression model';
$string['linear'] = 'Basic simple linear regression';

$string['arenotstudent'] = 'You are not student in this context!';

