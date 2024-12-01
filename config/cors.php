<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'], 

    'allowed_methods' => ['*'], 

    'allowed_origins' => ['https://seppedu.com', 'https://api.seppedu.com'], 
    
    'allowed_origins_patterns' => [], 

    'allowed_headers' => [':authority:', ':method:', ':path:', ':scheme:'], // this is the line i needed to update to solve the issue.

    // 'allowed_headers' => ['*'], 

    'exposed_headers' => [], 

    'max_age' => 0, 

    'supports_credentials' => true, 
];


// return [

//     /*
//     |--------------------------------------------------------------------------
//     | Cross-Origin Resource Sharing (CORS) Configuration
//     |--------------------------------------------------------------------------

//     */

//     'paths' => ['api/*', 'sanctum/csrf-cookie'],

//     'allowed_methods' => ['*'],

//     // 'allowed_origins' => ['*'],
//       'allowed_origins' => ['https://seppedu.com'] ,
//       // 'allowed_origins' => ['seppedu.com' || 'http://localhost:3000'],

//     'allowed_origins_patterns' => [],

//     'allowed_headers' => ['*'],

//     'exposed_headers' => [],

//     'max_age' => 0,

//     'supports_credentials' => true,

// ];

