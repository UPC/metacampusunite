<?php

require_once(__DIR__ . '/../../config.php');

use local_externaldocument\controller\extdoc_list;

$selectedtype = optional_param('type', 'course', PARAM_TEXT);

extdoc_list::setup_page($selectedtype);
extdoc_list::render_page($selectedtype);