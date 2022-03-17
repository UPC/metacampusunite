<?php

require_once(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);
$type = required_param('type', PARAM_TEXT);

$extdoc = \local_externaldocument\extdoc::instance($type);
$extdoc->delete($id);

$return_url = new moodle_url('/local/externaldocument/test-list.php', array('type' => $type));
redirect($return_url, 'Esborrat external document tipus ' . $type . ' amb ID ' . $id, null, \core\output\notification::NOTIFY_SUCCESS);