<?php

require 'vendor/autoload.php'; // Ensure this path is correct

use OpenApi\Generator;

// Generate the OpenAPI documentation
$openapi = Generator::scan(['controllers', 'models', 'repositories', 'services']); // Adjust as necessary

header('Content-Type: application/json');
echo $openapi->toJson();
