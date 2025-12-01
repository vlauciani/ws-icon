<?php
/**
 * event1.php - DEPRECATED: Redirects to circle.php
 * This endpoint is deprecated. Use circle.php instead.
 */

require("functions.php");

write_string_to_file(LOG_FILE, "DEPRECATED: event1.php -> redirecting to circle.php");

// Build query string without type parameter
$queryParams = $_GET;
unset($queryParams['type']); // Remove type if present

// Build new URL
$newUrl = 'circle.php';
if (!empty($queryParams)) {
    $newUrl .= '?' . http_build_query($queryParams);
}

// Log deprecation usage
$clientIP = getClientIP();
write_string_to_file(LOG_FILE, "Deprecated endpoint 'event1.php' accessed from {$clientIP} - redirecting to {$newUrl}");

// Perform HTTP 301 Permanent Redirect
header('Location: ' . $newUrl, true, 301);
header('X-Deprecated-Endpoint: event1.php');
header('X-Preferred-Endpoint: circle.php');
header('X-Deprecation-Info: This endpoint is deprecated. Use circle.php instead. This redirect will be removed in v3.0.');
exit;
