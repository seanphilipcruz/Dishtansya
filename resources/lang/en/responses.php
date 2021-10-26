<?php

return [
    'unauthorized' => 'Unauthorized request.',
    'login' => [
        'failed' => [
            'credentials' => 'Email and password does not exist in the database.',
        ],
    ],
    'register' => [
        'failed' => [
            'existing_email' => 'The email provided has already been taken.',
        ],
        'success' => [
            'complete' => 'Registration complete.'
        ],
    ],
    'logout' => [
        'success' => 'Logout success.'
    ],
    'order' => [
        'failed' => [
            'invalid_stocks' => 'Failed to order the product due to the unavailability of the stock.'
        ],
        'success' => [
            'complete' => 'You have successfully ordered this product.'
        ]
    ]
];
