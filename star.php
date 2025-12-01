<?php
/**
 * star.php - Star icon generation endpoint
 * Generates 5-pointed star icons with customizable colors and border
 */

require("functions.php");
require_once('./error_handler.php');
require_once('./types/TypeStar.php');

$startTime = microtime(true);

write_string_to_file(LOG_FILE, "START: ---------- star icon ----------");

// Convert GET parameters to lowercase for case-insensitive processing
$_GET_lower = array_change_key_case($_GET, CASE_LOWER);

// Validate parameters
$validationErrors = validateStarParams($_GET_lower);
if (!empty($validationErrors)) {
    $arrayError['CodeDescription'] = 'Invalid parameters';
    $arrayError['ValidationErrors'] = $validationErrors;
    reportErrorAndExit($arrayError);
}

// Generate icon filename based on all parameters
$iconFilename = getStarFilename($_GET_lower);
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
    $image = generateStarIcon($_GET_lower);

    // Save to cache
    saveIconToCache($image, $iconFilenameFullPath);

    imagedestroy($image);
}

// Load and serve icon from cache
serveIconFromCache($iconFilenameFullPath, $iconFilename, $cacheHit, 'star');

write_string_to_file(LOG_FILE, "END: ---------- star icon ----------");
