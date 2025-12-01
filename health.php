<?php
/**
 * health.php - Health check endpoint for monitoring
 * Returns JSON with service health status
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

$health = [
    'status' => 'healthy',
    'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
    'checks' => []
];

// Check if icons directory exists and is writable
$iconsDir = '/tmp/icons';
if (file_exists($iconsDir) && is_dir($iconsDir) && is_writable($iconsDir)) {
    $health['checks']['icons_directory_writable'] = true;
} else {
    $health['checks']['icons_directory_writable'] = false;
    $health['status'] = 'unhealthy';
}

// Check if GD library is available
if (extension_loaded('gd')) {
    $health['checks']['gd_library'] = true;
} else {
    $health['checks']['gd_library'] = false;
    $health['status'] = 'unhealthy';
}

// Check if required fonts are available
$fontsAvailable = true;
$requiredFonts = ['arialbd.ttf', 'arial.ttf'];
foreach ($requiredFonts as $font) {
    if (!file_exists(__DIR__ . '/' . $font)) {
        $fontsAvailable = false;
        break;
    }
}
$health['checks']['fonts_available'] = $fontsAvailable;
if (!$fontsAvailable) {
    $health['status'] = 'unhealthy';
}

// Check version file
if (file_exists(__DIR__ . '/version')) {
    $health['version'] = trim(file_get_contents(__DIR__ . '/version'));
} else {
    $health['version'] = 'unknown';
}

// Set appropriate HTTP status code
if ($health['status'] === 'healthy') {
    http_response_code(200);
} else {
    http_response_code(503); // Service Unavailable
}

echo json_encode($health, JSON_PRETTY_PRINT);
