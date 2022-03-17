<?php

$capabilities = [
    'filter/fulltranslate:edittranslations' => [
        'captype' => 'write',
        'riskbitmaskt' => 'RISK_CONFIG',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    ]
];