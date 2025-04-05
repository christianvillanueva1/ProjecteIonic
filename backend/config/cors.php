<?php
return [
    'paths' => ['api/*', 'login'],  // Afegir tots els camins que necessiten CORS
    'allowed_methods' => ['*'],  // Permet totes les sol·licituds HTTP (GET, POST, etc.)
    'allowed_origins' => ['http://localhost:8100'],  // Permetre el teu domini d'origen (Ionic)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
