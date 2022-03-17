<?php

namespace local_externaldocument\controller;

use single_select;

class extdoc_typeselector {

    private $selectedtype = '';

    public function __construct($selectedtype = '') {
        $this->set_selectedtype($selectedtype);
    }

    private function set_selectedtype($type) {
        $this->selectedtype = $type;
    }

    public function render_type_selector(): void {
        global $OUTPUT, $PAGE;

        $types = $this->get_types();
        $select = new single_select($PAGE->url, 'type', $types, $this->selectedtype);
        echo $OUTPUT->render($select);
    }

    public function get_types(): array {
        $raw_types = scandir(__DIR__ . '/../types');
        array_walk($raw_types, function (&$value, $key) use (&$raw_types, &$types) {
            if ($value != '.' && $value != '..') {
                $formatted_type = explode('.', $value)[0];
                $types[$formatted_type] = ucfirst($formatted_type);
            }
        });
        return $types;
    }

}