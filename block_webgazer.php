<?php
// This file is part of a module for Moodle - http://moodle.org/
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
 * WebGazer as a moodle block
 *
 * @package    block_webgazer
 * @copyright  Geiser Chalco <geiser@usp.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_webgazer extends block_base {

    private $sessionid = FALSE;
    private $iswebgazerinit = FALSE;

    function init() {
        global $DB, $USER, $PAGE;

        $this->title = get_string('pluginname', 'block_webgazer');
        if (!user_has_role_assignment($USER->id, 5)) return;
        
        // initialize webgazer session
        $webgazersession = new stdClass();
        $webgazersession->clockstart = time();
        $webgazersession->url = $PAGE->url->out_as_local_url();
        $webgazersession->userid = $USER->id;
        $webgazersession->contextid = $PAGE->context->id;
        $webgazersession->courseid = $PAGE->course->id;
        $webgazersession->cmid = $PAGE->cm->id;
        $this->sessionid = $DB->insert_record('webgazer_session', $webgazersession);
    }
    
    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_allow_multiple() {
        return false;
    }

    function has_config() { return true; }

    function get_content() {
        global $DB, $USER, $PAGE;
        
        $this->content = new stdClass;
        if (!user_has_role_assignment($USER->id, 5)) {
            $this->content->text = get_string('arenotstudent', 'block_webgazer');
            $this->content->footer = 'Timestamp: '.time(); 
            return $this->content;
        }

        if (!$this->iswebgazerinit && $this->sessionid) {
            $PAGE->requires->js('/blocks/webgazer/js/webgazer.min.js', true);
            $PAGE->requires->js('/blocks/webgazer/js/html2canvas.min.js', true);
        
            $config = get_config('block_webgazer');

            // setting parameters 
            $autosavetime = intval($config->autosavetime) * 1000;
            if (!is_null($this->config->autosavetime)) {
                $autosavetime = intval($this->config->autosavetime) * 1000;
            }

            $enablescreenshot = !empty($config->enablescreenshot);
            if (!is_null($this->config->enablescreenshot)) {
                $enablescreenshot = !empty($this->config->enablescreenshot);
            }

            $showpredictionpoint = !empty($config->showpredictionpoint);
            if (!is_null($this->config->showpredictionpoint)) {
                $showpredictionpoint = $this->config->showpredictionpoint;
            }

            $showvideocanvas = !empty($config->showvideocanvas);
            if (!is_null($this->config->showvideocanvas)) {
                $showvideocanvas = $this->config->showvideocanvas;
            }
        
            $PAGE->requires->js_call_amd('block_webgazer/data_gathering', 'setParameters',
                array('autosavetime'=>$autosavetime, 'enablescreenshot'=>$enablescreenshot,
                      'showpredictionpoint'=>$showpredictionpoint, 'showvideocanvas'=>$showvideocanvas));
       
            // setting libraries
            $tracker = $config->tracker;
            if (!empty($this->config->tracker)) {
                $tracker = $this->config->tracker;
            }

            $regression = $config->regression;
            if (!empty($this->config->regression)) {
                $regression = $this->config->regression;
            }

            $PAGE->requires->js_call_amd('block_webgazer/data_gathering', 'setLibraries',
                array('tracker'=>$tracker, 'regression'=>$regression));

            $url = new moodle_url('/blocks/webgazer/ajax.php', array('sesskey'=>sesskey()));

            // start data gathering
            $PAGE->requires->js_call_amd('block_webgazer/data_gathering', 'init',
                array('sessionid'=>$this->sessionid, 'url'=>$url->out()));
            $this->iswebgazerinit = TRUE;
        }

        // setting the content for the block
        $this->content->text = '<div id="webgazerVideoDiv"></div>';
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '<div id="webgazerVideoText"></div>';

        return $this->content;
    }

}

