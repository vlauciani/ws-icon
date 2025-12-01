<?php
/**
 * icon.php - Backward compatibility router
 * Redirects to appropriate icon endpoint based on type parameter
 *
 * DEPRECATED: This file is maintained for backward compatibility only.
 * New code should use direct endpoints: square.php, circle.php, triangle.php
 */

require("functions.php");

write_string_to_file(LOG_FILE, "LEGACY: icon.php router accessed (backward compatibility mode)");

// Convert GET parameters to lowercase for case-insensitive processing
$_GET_lower = array_change_key_case($_GET, CASE_LOWER);

// Type mapping: maps type parameter to endpoint files
$typeMap = [
    'sta2'     => 'square.php',
    'square'   => 'square.php',
    'event1'   => 'circle.php',
    'circle'   => 'circle.php',
    'triangle' => 'triangle.php',
    'pentagon' => 'pentagon.php',
    'hexagon'  => 'hexagon.php',
    'star'     => 'star.php'
];

// Validate type parameter
if (!isset($_GET_lower['type'])) {
    require_once('./error_handler.php');
    $arrayError['Code'] = 400;
    $arrayError['CodeDescription'] = 'Parameter "type" is required';
    $arrayError['ValidationErrors'] = [[
        'parameter' => 'type',
        'message' => 'Parameter "type" is required',
        'expected' => 'One of: sta2, square, event1, circle, triangle, pentagon, hexagon, star',
        'hint' => 'Consider using direct endpoints: square.php, circle.php, triangle.php, pentagon.php, hexagon.php, star.php'
    ]];
    reportErrorAndExit($arrayError);
}

$type = $_GET_lower['type'];

// Check if type is valid
if (!isset($typeMap[$type])) {
    require_once('./error_handler.php');
    $arrayError['Code'] = 400;
    $arrayError['CodeDescription'] = 'Invalid type value';
    $arrayError['ValidationErrors'] = [[
        'parameter' => 'type',
        'message' => 'Invalid type value',
        'value' => $type,
        'expected' => 'One of: sta2, square, event1, circle, triangle, pentagon, hexagon, star',
        'hint' => 'Consider using direct endpoints: square.php, circle.php, triangle.php, pentagon.php, hexagon.php, star.php'
    ]];
    reportErrorAndExit($arrayError);
}

// Get target endpoint
$targetEndpoint = $typeMap[$type];

// Build query string without type parameter
$queryParams = $_GET;
unset($queryParams['type']);
unset($queryParams['TYPE']); // Remove uppercase version if exists

// Build new URL
$newUrl = $targetEndpoint;
if (!empty($queryParams)) {
    $newUrl .= '?' . http_build_query($queryParams);
}

// Log the routing
$clientIP = getClientIP();
$deprecationNote = in_array($type, ['sta2', 'event1']) ? ' (DEPRECATED type)' : '';
write_string_to_file(LOG_FILE, "Routing icon.php?type={$type}{$deprecationNote} from {$clientIP} -> {$newUrl}");

// Perform HTTP 302 Found (Temporary Redirect)
// Using 302 instead of 301 because this is a compatibility layer, not a permanent redirect
header('Location: ' . $newUrl, true, 302);
header('X-Legacy-Router: icon.php');
header('X-Routed-To: ' . $targetEndpoint);
header('X-Migration-Info: Direct endpoint access is preferred. Use ' . $targetEndpoint . ' instead of icon.php?type=' . $type);

if (in_array($type, ['sta2', 'event1'])) {
    $preferredType = ($type === 'sta2') ? 'square' : 'circle';
    header('X-Deprecated-Type: ' . $type);
    header('X-Preferred-Type: ' . $preferredType);
}

exit;
