<?php

$services = array(
    'local_externaldocument_service' => array(
        'functions'         => array('local_externaldocument_add'),
        'enabled'           => 1,
        'restrictedusers'   => 0
    )
);

$functions = array(
    'local_externaldocument_add' => array(
        'classname'     => 'local_externaldocument\external\externaldocument',
        'methodname'    => 'add',
        'classpath'     => '',
        'description'   => 'Add a new document to the database',
        'type'          => 'write',
    )
);