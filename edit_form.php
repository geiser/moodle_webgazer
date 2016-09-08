<?php

class block_webgazer_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        $config = get_config('block_webgazer');

        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Setting variables with default values
        $mform->addElement('text', 'config_autosavetime',
            get_string('autosavetime', 'block_webgazer'));
        $mform->setType('config_autosavetime', PARAM_INT);
        $mform->setDefault('config_autosavetime', $config->autosavetime);
        $mform->addHelpButton('config_autosavetime', 'autosavetime', 'block_webgazer');

        $mform->addElement('advcheckbox', 'config_enablescreenshot',
            get_string('enablescreenshot', 'block_webgazer'),
            get_string('enablescreenshot_desc', 'block_webgazer'));
        $mform->setType('config_enablescreenshot', PARAM_INT);
        $mform->setDefault('config_enablescreenshot', $config->enablescreenshot);
        $mform->addHelpButton('config_enablescreenshot', 'enablescreenshot', 'block_webgazer');

        $mform->addElement('advcheckbox', 'config_showpredictionpoint',
            get_string('showpredictionpoint', 'block_webgazer'),
            get_string('showpredictionpoint_desc', 'block_webgazer'));
        $mform->setType('config_showpredictionpoint', PARAM_INT);
        $mform->setDefault('config_showpredictionpoint', $config->showpredictionpoint);
        $mform->addHelpButton('config_showpredictionpoint', 'showpredictionpoint', 'block_webgazer');

        $mform->addElement('advcheckbox', 'config_showvideocanvas',
            get_string('showvideocanvas', 'block_webgazer'),
            get_string('showvideocanvas_desc', 'block_webgazer'));
        $mform->setType('config_showvideocanvas', PARAM_INT);
        $mform->setDefault('config_showvideocanvas', $config->showvideocanvas);
        $mform->addHelpButton('config_showvideocanvas', 'showvideocanvas', 'block_webgazer');
        
        $tracker_opts = array('clmtrackr' => get_string('clmtrackr', 'block_webgazer'),
                              'trackingjs' => get_string('trackingjs', 'block_webgazer'),
                              'js_objectdetect' => get_string('js_objectdetect', 'block_webgazer'));
        $mform->addElement('select', 'config_tracker', get_string('tracker', 'block_webgazer'), $tracker_opts);
        $mform->setDefault('config_tracker', $config->tracker);
        $mform->addHelpButton('config_tracker', 'tracker', 'block_webgazer');
        
        $regression_opts = array('ridge' => get_string('ridge', 'block_webgazer'),
                                 'weightedRidge' => get_string('weightedRidge', 'block_webgazer'),
                                 'threadedRidge' => get_string('threadedRidge', 'block_webgazer'),
                                 'linear' => get_string('linear', 'block_webgazer'));
        $mform->addElement('select', 'config_regression',
            get_string('regression', 'block_webgazer'), $regression_opts);
        $mform->setDefault('config_regression', $config->regression);
        $mform->addHelpButton('config_regression', 'regression', 'block_webgazer');
       
    }
}
