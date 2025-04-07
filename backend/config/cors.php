<?php
return [
    'paths' => ['api/*', 'login'],  // Afegir tots els camins que necessiten CORS
    'allowed_methods' => ['*'],  // Permetre totes les sol·licituds HTTP (GET, POST, etc.)
    'allowed_origins' => ['*'],  // Permetre el teu domini d'origen (Ionic)
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
