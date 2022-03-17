<?php

function filter_fulltranslate_before_footer() {
    global $PAGE, $SESSION, $CFG;
    if (isset($SESSION->filter_fulltranslate->strings)) {
        $strings = $SESSION->filter_fulltranslate->strings;
    } else {
        $strings = [];
    }

    if (has_capability('filter/fulltranslate:edittranslations', \context_system::instance())) {
        if (get_config('filter_fulltranslate', 'showstringsinfooter') !=  0) {
            foreach ($strings as $key => $value) {
                $id = $key;
                echo  $value .' <a target="_blank" data-action="translation-edit" data-recordid="'.$id.'" href="'.$CFG->wwwroot.'/admin/tool/translationmanager/edit.php?id='.$id.'">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><br/>';
            }
        }
        $PAGE->requires->js_call_amd('tool_translationmanager/translationmanager', 'init');
    }
    unset($SESSION->filter_fulltranslate->strings);
}