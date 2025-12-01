<?php
/**
 * spec.php - Serves the OpenAPI specification file
 */

header('Content-Type: application/x-yaml');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

$yamlFile = __DIR__ . '/openapi.yaml';

if (file_exists($yamlFile)) {
    readfile($yamlFile);
} else {
    http_response_code(404);
    echo "OpenAPI specification not found.";
}
