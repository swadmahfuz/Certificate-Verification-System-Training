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

    'cache_ttl' => [
        'dashboard' => (int) env('CVS_DASHBOARD_CACHE_TTL', 300),
        'permissions' => (int) env('CVS_PERMISSIONS_CACHE_TTL', 900),
    ],

    'certificate_search' => [
        'like' => [
            'certificate_number',
            'participant_name',
            'company',
            'training_name',
            'location',
            'trainer',
        ],
        'exact' => [
            'passport_nid',
            'driving_license',
        ],
        'date_like' => [
            'training_date',
            'training_end',
            'issue_date',
            'expiry_date',
        ],
    ],
];
