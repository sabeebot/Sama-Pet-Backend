<?php

return [



    'paths' => ['api/*', 'sanctum/csrf-cookie', 'admin/*'],

'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],

    'allowed_origins' => ['http://localhost:4200'], 

    'allowed_origins_patterns' => [],

    // Allow all headers. Consider adding specific headers if required.
    'allowed_headers' => ['*'], 

    // Expose headers that are safe to expose to the browser
    'exposed_headers' => [],

    // Max age of the CORS preflight request, set to 24 hours
    'max_age' => 86400, // 24 hours (in seconds)

    // Allow credentials such as cookies and authentication headers
    'supports_credentials' => true,

];
