<?php

return [
    'app_key' => env('CVS_APP_KEY', 'training'),

    'registration_enabled' => env('CVS_REGISTRATION_ENABLED', false),

    'apps' => [
        'training' => 'Training CVS',
        'inspection' => 'Inspection CVS',
        'calibration' => 'Calibration CVS',
        'reports' => 'Reports CVS',
        'certification' => 'BA Certification',
    ],

    'access_levels' => [
        'view' => 'View only',
        'full' => 'Full access',
    ],

    'shared_activity_subject_types' => ['auth', 'user', 'department'],
];
