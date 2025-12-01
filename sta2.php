<?php
/**
 * sta2.php - DEPRECATED: Redirects to square.php
 * This endpoint is deprecated. Use square.php instead.
 */

require("functions.php");

write_string_to_file(LOG_FILE, "DEPRECATED: sta2.php -> redirecting to square.php");

// Build query string without type parameter
$queryParams = $_GET;
unset($queryParams['type']); // Remove type if present

// Build new URL
$newUrl = 'square.php';
if (!empty($queryParams)) {
    $newUrl .= '?' . http_build_query($queryParams);
}

// Log deprecation usage
$clientIP = getClientIP();
write_string_to_file(LOG_FILE, "Deprecated endpoint 'sta2.php' accessed from {$clientIP} - redirecting to {$newUrl}");

// Perform HTTP 301 Permanent Redirect
header('Location: ' . $newUrl, true, 301);
header('X-Deprecated-Endpoint: sta2.php');
header('X-Preferred-Endpoint: square.php');
header('X-Deprecation-Info: This endpoint is deprecated. Use square.php instead. This redirect will be removed in v3.0.');
exit;
