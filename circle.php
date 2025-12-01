<?php
/**
 * circle.php - Circle icon generation endpoint
 * Generates circular icons with customizable radius and colors
 */

require("functions.php");
require_once('./error_handler.php');
require_once('./types/TypeCircle.php');

$startTime = microtime(true);

write_string_to_file(LOG_FILE, "START: ---------- circle icon ----------");

// Convert GET parameters to lowercase for case-insensitive processing
$_GET_lower = array_change_key_case($_GET, CASE_LOWER);

// Validate parameters
$validationErrors = validateCircleParams($_GET_lower);
if (!empty($validationErrors)) {
    $arrayError['CodeDescription'] = 'Invalid parameters';
    $arrayError['ValidationErrors'] = $validationErrors;
    reportErrorAndExit($arrayError);
}

// Generate icon filename based on all parameters
$iconFilename = getCircleFilename($_GET_lower);
$iconFilenameFullPath = DIR_ICONS . '/' . $iconFilename;

write_string_to_file(LOG_FILE, "ip=" . getClientIP() . " | iconFilename={$iconFilename}");

// Check if icon file already exists in cache
$cacheHit = false;
if (file_exists($iconFilenameFullPath)) {
    write_string_to_file(LOG_FILE, "Image exists (cache HIT): $iconFilenameFullPath");
    $cacheHit = true;
} else {
    write_string_to_file(LOG_FILE, "Image doesn't exist (cache MISS). Generating...");

    // Generate the icon
    $image = generateCircleIcon($_GET_lower);

    // Save to cache
    saveIconToCache($image, $iconFilenameFullPath);

    imagedestroy($image);
}

// Load and serve icon from cache
serveIconFromCache($iconFilenameFullPath, $iconFilename, $cacheHit, 'circle');

write_string_to_file(LOG_FILE, "END: ---------- circle icon ----------");
