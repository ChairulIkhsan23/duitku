<?php
/**
 * Laravel CORS Configuration
 * This configuration file determines how Cross-Origin Resource Sharing (CORS) requests are handled by the application.
*/
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'], 
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, 
];