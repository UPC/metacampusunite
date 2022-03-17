<?php

function tool_translationmanager_output_fragment_translation_form($id) {
    global $DB;
    $record = $DB->get_record('filter_fulltranslate', ['id' => $id['formdata']]);
    $actionurl = null;
    if ($record->textformat == 'html') {
        $record->translation = ['text' => $record->translation];
    }
    // Pass the source type as custom data so it can by used to detetmine the type of edit.
    $customdata = ['textformat' => $record->textformat, 'length' => strlen($record->sourcetext)];
    $form = new \tool_translationmanager\form\translation_form($actionurl, $customdata, 'post', '', [], true, $record);
    $form->validate_defined_fields(true);
    $form->set_data($record);

    return $form->render();
}