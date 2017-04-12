<?php

return [
    // debug mode?
    'debug' => false,

    // Set the user model.
    'user_model' => env('MARDIN_USER_MODEL', 'App\\User'),

    // Set the user model transformer.
    'user_transformer' => env('MARDIN_USER_TRANSFORMER', 'App\\Transformers\\UserTransformer'),

    // Route related options.
    'routes' => [
        // Set the prefix that should be used for routes
        'prefix' => env('MARDIN_ROUTE_PREFIX', 'messages'),

        // Set the bindings for guided routes.
        'bindings' => [
            // public
            'public' => [
                'middleware' => 'web',
            ],

            // admin
            'admin' => [
                // 'middleware' => 'admin',
            ],
        ],
    ],

    // ga Ad for react component
    'ad' => [
        'client' => false,
        'slot_id' => false,
    ],
];
