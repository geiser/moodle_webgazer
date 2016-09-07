# webgazer
Moodle block to collect information using webgazer

## Guidelines
How to show the screenshot taken by the module in moodle

    $image = $DB->get_field('webgazer_session','screenshot', array('id'=>$sessionid));
    echo '<img src="'.$image.'"/>';

