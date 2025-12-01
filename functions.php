<?php
/**
 * functions.php - Common utilities and image generation functions
 */

date_default_timezone_set('UTC');

$arrayError = array();
$arrayError['Code'] = 400;
$borderSizeDefault = 3;

// Log file variables
define('LOG_ENABLED', filter_var(getenv('LOG_ENABLED'), FILTER_VALIDATE_BOOLEAN) ?: false);
define('LOG_FILE', getenv('LOG_FILE') ?: '/tmp/log/' . date("Y-m-d") . '__ws_icon.log');
define('LOG_TO_STDOUT', filter_var(getenv('LOG_TO_STDOUT'), FILTER_VALIDATE_BOOLEAN) ?: true);
// Other variables
define('SERVICE_NAME', 'ws-icon');
define('VERSION_FILE', './version');
define('EMAIL_SUBJECT', 'WS Icon');
define('REMOVE_LOG_FILES_OLDER_THAN', getenv('REMOVE_LOG_FILES_OLDER_THAN') ?: 10); // Define the age threshold for removing old log files (in days)
define('DIR_ICONS', '/tmp/icons');
define('RANDOM_ALPHANUMERIC_STRING', randomAlphaNumericString());
define('DEFAULT_FONTSIZE', 10);

// Deprecated type mappings
$deprecatedTypes = [
    'sta2' => 'square',
    'event1' => 'circle'
];

// *************************************************************************
// Validation Functions
// *************************************************************************

/**
 * Validate hex color format
 * @param string $color Hex color code (6 characters)
 * @return bool True if valid hex color
 */
function isValidHexColor($color) {
    return preg_match('/^[0-9A-Fa-f]{6}$/', $color);
}

function sendEmail($subject, $message)
{
    $to      = 'valentino.lauciani@ingv.it';
    $headers = 'From: valentino.lauciani@ingv.it' . "\r\n" .
        'Reply-To: valentino.lauciani@ingv.it' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
}

//Generate a random alphaNumeric String
function randomAlphaNumericString($howLenght = 5)
{
    $_GET_lower = array_change_key_case($_GET, CASE_LOWER);
    $unique_key = "";
    if (isset($_GET_lower['user'])) {
        $unique_key .= $_GET_lower['user'] . '-';
    }
    $unique_key .= substr(md5(rand(0, 1000000)), 0, $howLenght);
    return $unique_key;
}

// Return DateTime object including microtime for "now"
function dto_now()
{
    list($usec, $sec) = explode(' ', microtime());
    $usec = substr($usec, 2, 6);
    $datetime_now = date('Y-m-d H:i:s\.', $sec) . $usec;
    return $datetime_now;
}

function cleanup_old_log_files($directory, $days)
{
    $files = glob($directory . '/*'); // Get all files in the directory
    $now = time();
    $cutoff = $now - ($days * 24 * 60 * 60); // Calculate the cutoff timestamp

    foreach ($files as $file) {
        if (is_file($file)) {
            if (filemtime($file) < $cutoff) {
                unlink($file); // Delete the file if it is older than the cutoff timestamp
            }
        }
    }
}

function write_string_to_file($filename, $text)
{
    global $arrayError;

    if (LOG_ENABLED) {
        $now = dto_now();
        $string = '[' . $now . ' | ' . RANDOM_ALPHANUMERIC_STRING . '] - ' . $text . "\n";

        if (preg_match("/START/", $text)) {
            $tmp = "\n" . $string;
            $string = $tmp;
        }

        if (LOG_TO_STDOUT) {
            $out = fopen('php://stdout', 'w');
            fputs($out, $string);
            fclose($out);
        } else {
            // Remove old log files; only perform cleanup in even minutes
            $directory = dirname($filename);
            if (intval(date('i')) % 2 == 0) {
                cleanup_old_log_files($directory, REMOVE_LOG_FILES_OLDER_THAN);
            }

            if (file_exists(dirname($filename))) {
                if (is_writable(dirname($filename))) {
                    if (!$handle = fopen($filename, 'a')) {
                        $arrayError['CodeDescription'] = 'Cannot open file (' . $filename . ')';
                        reportErrorAndExit($arrayError, "n");
                    }

                    // Write $string to our opened file.
                    if (fwrite($handle, $string) === FALSE) {
                        $arrayError['CodeDescription'] = 'Cannot write to file (' . $filename . ')';
                        reportErrorAndExit($arrayError, "n");
                    }
                    fclose($handle);
                } else {
                    $arrayError['CodeDescription'] = "The directory \"" . dirname("$filename") . "\" is not writable, set to 777.";
                    reportErrorAndExit($arrayError, "n");
                }
            } else {
                $arrayError['CodeDescription'] = "The directory \"" . dirname("$filename") . "\" doesn't exist.";
                reportErrorAndExit($arrayError, "n");
            }
        }
    }
}

function getWSVersionFromFile($file)
{
    return file_get_contents("$file", FILE_USE_INCLUDE_PATH);
}

// Implemetation of 'http_response_code' function; it is present on PHP >= 5.4.0
if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL)
    {
        if ($code !== NULL) {
            switch ($code) {
                case 100:
                    $text = 'Continue';
                    break;
                case 101:
                    $text = 'Switching Protocols';
                    break;
                case 200:
                    $text = 'OK';
                    break;
                case 201:
                    $text = 'Created';
                    break;
                case 202:
                    $text = 'Accepted';
                    break;
                case 203:
                    $text = 'Non-Authoritative Information';
                    break;
                case 204:
                    $text = 'No Content';
                    break;
                case 205:
                    $text = 'Reset Content';
                    break;
                case 206:
                    $text = 'Partial Content';
                    break;
                case 300:
                    $text = 'Multiple Choices';
                    break;
                case 301:
                    $text = 'Moved Permanently';
                    break;
                case 302:
                    $text = 'Moved Temporarily';
                    break;
                case 303:
                    $text = 'See Other';
                    break;
                case 304:
                    $text = 'Not Modified';
                    break;
                case 305:
                    $text = 'Use Proxy';
                    break;
                case 400:
                    $text = 'Bad Request';
                    break;
                case 401:
                    $text = 'Unauthorized';
                    break;
                case 402:
                    $text = 'Payment Required';
                    break;
                case 403:
                    $text = 'Forbidden';
                    break;
                case 404:
                    $text = 'Not Found';
                    break;
                case 405:
                    $text = 'Method Not Allowed';
                    break;
                case 406:
                    $text = 'Not Acceptable';
                    break;
                case 407:
                    $text = 'Proxy Authentication Required';
                    break;
                case 408:
                    $text = 'Request Time-out';
                    break;
                case 409:
                    $text = 'Conflict';
                    break;
                case 410:
                    $text = 'Gone';
                    break;
                case 411:
                    $text = 'Length Required';
                    break;
                case 412:
                    $text = 'Precondition Failed';
                    break;
                case 413:
                    $text = 'Request Entity Too Large';
                    break;
                case 414:
                    $text = 'Request-URI Too Large';
                    break;
                case 415:
                    $text = 'Unsupported Media Type';
                    break;
                case 500:
                    $text = 'Internal Server Error';
                    break;
                case 501:
                    $text = 'Not Implemented';
                    break;
                case 502:
                    $text = 'Bad Gateway';
                    break;
                case 503:
                    $text = 'Service Unavailable';
                    break;
                case 504:
                    $text = 'Gateway Time-out';
                    break;
                case 505:
                    $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }
        return $code;
    }
}

/**
 * Report error and exit with JSON response
 * @param array $arrayError Error information
 * @param string $writeStringToFile Whether to log ("y" or "n")
 */
function reportErrorAndExit($arrayError, $writeStringToFile = "y")
{
    if ($writeStringToFile == "y") {
        write_string_to_file(LOG_FILE, "reportErrorAndExit: " . json_encode($arrayError));
    }

    $request = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    $requestSubmitted = date("Y-m-d\TH:i:s T");
    $serviceVersion = SERVICE_NAME . ': v' . getWSVersionFromFile(VERSION_FILE);

    // Prepare JSON error response
    $errorResponse = [
        'error' => true,
        'code' => $arrayError['Code'],
        'message' => $arrayError['CodeDescription'],
        'request' => $request,
        'timestamp' => $requestSubmitted,
        'service_version' => $serviceVersion
    ];

    // Add validation errors if present
    if (isset($arrayError['ValidationErrors']) && !empty($arrayError['ValidationErrors'])) {
        $errorResponse['validation_errors'] = $arrayError['ValidationErrors'];
    }

    http_response_code($arrayError['Code']);
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *"); // CORS support
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    echo json_encode($errorResponse, JSON_PRETTY_PRINT);

    // Sending eMail
    if ($writeStringToFile == "y") {
        write_string_to_file(LOG_FILE, "Sending Check Error eMail");
    }
    $subject = EMAIL_SUBJECT . " - Check Error";

    $message  = "From: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $message .= "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n\n";
    $message .= "Error: " . $arrayError['Code'] . "\n";
    $message .= "Bad Request: " . $arrayError['CodeDescription'] . "\n\n";
    $message .= "Request: " . $request . "\n";
    $message .= "Request Submitted: " . $requestSubmitted . "\n";
    $message .= "Service version: " . $serviceVersion;
    sendEmail($subject, $message);

    if ($writeStringToFile == "y") {
        write_string_to_file(LOG_FILE, "END: ---------- icon ----------");
    }
    exit;
}

// *************************************************************************
// Image Generation Functions
// *************************************************************************

// Function to create a colored square
function createSquare($image, $width, $height, $color, $borderSize, $borderColorRGB)
{
    // Create square without border
    $square = imagecreatetruecolor($width, $height);
    $fillColor = imagecolorallocate($square, $color['r'], $color['g'], $color['b']);
    imagefilledrectangle($square, 0, 0, $width - 1, $height - 1, $fillColor);

    // Add border
    $borderColor = imagecolorallocate($square, $borderColorRGB['r'], $borderColorRGB['g'], $borderColorRGB['b']);
    for ($i = 0; $i < $borderSize; $i++) {
        imagerectangle($square, $i, $i, $width - 1 - $i, $height - 1 - $i, $borderColor);
    }

    return $square;
}

/**
 * Create a colored circle with transparent background
 * @param int $radius Circle radius in pixels
 * @param array $color RGB color array ['r' => 0-255, 'g' => 0-255, 'b' => 0-255]
 * @return resource GD image resource
 */
function createCircle($radius, $color)
{
    // Create image with transparent background
    $circle = imagecreatetruecolor($radius * 2, $radius * 2);
    imagesavealpha($circle, true);
    $transparent = imagecolorallocatealpha($circle, 0, 0, 0, 127);
    imagefill($circle, 0, 0, $transparent);

    // Allocate fill color
    $fillColor = imagecolorallocate($circle, $color['r'], $color['g'], $color['b']);

    // Draw filled ellipse (circle) on transparent background
    imagefilledellipse($circle, $radius, $radius, $radius * 2, $radius * 2, $fillColor);

    return $circle;
}

// Function to create a colored triangle
function createTriangle($size, $bgColor, $borderSize, $borderColor)
{
    // Create image with transparent background
    $triangle = imagecreatetruecolor($size, $size);
    imagesavealpha($triangle, true);
    $transparent = imagecolorallocatealpha($triangle, 0, 0, 0, 127);
    imagefill($triangle, 0, 0, $transparent);

    // Allocate colors
    $fillColor = imagecolorallocate($triangle, $bgColor['r'], $bgColor['g'], $bgColor['b']);
    $borderColorAlloc = imagecolorallocate($triangle, $borderColor['r'], $borderColor['g'], $borderColor['b']);

    // Define triangle points (equilateral triangle pointing up)
    $points = array(
        $size / 2, $borderSize,              // Top point
        $borderSize, $size - $borderSize,    // Bottom left
        $size - $borderSize, $size - $borderSize  // Bottom right
    );

    // Draw filled triangle
    imagefilledpolygon($triangle, $points, $fillColor);

    // Draw border
    if ($borderSize > 0) {
        imagesetthickness($triangle, $borderSize);
        imagepolygon($triangle, $points, $borderColorAlloc);
    }

    return $triangle;
}

// Function to create a colored pentagon
function createPentagon($size, $bgColor, $borderSize, $borderColor)
{
    // Create image with transparent background
    $pentagon = imagecreatetruecolor($size, $size);
    imagesavealpha($pentagon, true);
    $transparent = imagecolorallocatealpha($pentagon, 0, 0, 0, 127);
    imagefill($pentagon, 0, 0, $transparent);

    // Allocate colors
    $fillColor = imagecolorallocate($pentagon, $bgColor['r'], $bgColor['g'], $bgColor['b']);
    $borderColorAlloc = imagecolorallocate($pentagon, $borderColor['r'], $borderColor['g'], $borderColor['b']);

    // Define pentagon points (5 vertices, pointing up)
    $centerX = $size / 2;
    $centerY = $size / 2;
    $radius = ($size / 2) - $borderSize - 2;

    $points = array();
    for ($i = 0; $i < 5; $i++) {
        $angle = deg2rad(($i * 72) - 90); // -90 to start from top
        $points[] = $centerX + ($radius * cos($angle));
        $points[] = $centerY + ($radius * sin($angle));
    }

    // Draw filled pentagon
    imagefilledpolygon($pentagon, $points, $fillColor);

    // Draw border
    if ($borderSize > 0) {
        imagesetthickness($pentagon, $borderSize);
        imagepolygon($pentagon, $points, $borderColorAlloc);
    }

    return $pentagon;
}

// Function to create a colored hexagon
function createHexagon($size, $bgColor, $borderSize, $borderColor)
{
    // Create image with transparent background
    $hexagon = imagecreatetruecolor($size, $size);
    imagesavealpha($hexagon, true);
    $transparent = imagecolorallocatealpha($hexagon, 0, 0, 0, 127);
    imagefill($hexagon, 0, 0, $transparent);

    // Allocate colors
    $fillColor = imagecolorallocate($hexagon, $bgColor['r'], $bgColor['g'], $bgColor['b']);
    $borderColorAlloc = imagecolorallocate($hexagon, $borderColor['r'], $borderColor['g'], $borderColor['b']);

    // Define hexagon points (6 vertices, flat top)
    $centerX = $size / 2;
    $centerY = $size / 2;
    $radius = ($size / 2) - $borderSize - 2;

    $points = array();
    for ($i = 0; $i < 6; $i++) {
        $angle = deg2rad(($i * 60) - 90); // -90 to start from top
        $points[] = $centerX + ($radius * cos($angle));
        $points[] = $centerY + ($radius * sin($angle));
    }

    // Draw filled hexagon
    imagefilledpolygon($hexagon, $points, $fillColor);

    // Draw border
    if ($borderSize > 0) {
        imagesetthickness($hexagon, $borderSize);
        imagepolygon($hexagon, $points, $borderColorAlloc);
    }

    return $hexagon;
}

// Function to create a colored star (5-pointed)
function createStar($size, $bgColor, $borderSize, $borderColor)
{
    // Create image with transparent background
    $star = imagecreatetruecolor($size, $size);
    imagesavealpha($star, true);
    $transparent = imagecolorallocatealpha($star, 0, 0, 0, 127);
    imagefill($star, 0, 0, $transparent);

    // Allocate colors
    $fillColor = imagecolorallocate($star, $bgColor['r'], $bgColor['g'], $bgColor['b']);
    $borderColorAlloc = imagecolorallocate($star, $borderColor['r'], $borderColor['g'], $borderColor['b']);

    // Define star points (5-pointed star = 10 vertices alternating between outer and inner)
    $centerX = $size / 2;
    $centerY = $size / 2;
    $outerRadius = ($size / 2) - $borderSize - 2;
    $innerRadius = $outerRadius * 0.38; // Inner radius ratio for a nice-looking star

    $points = array();
    for ($i = 0; $i < 10; $i++) {
        $angle = deg2rad(($i * 36) - 90); // 36 degrees between each point (360/10), -90 to start from top
        $radius = ($i % 2 === 0) ? $outerRadius : $innerRadius; // Alternate between outer and inner points
        $points[] = $centerX + ($radius * cos($angle));
        $points[] = $centerY + ($radius * sin($angle));
    }

    // Draw filled star
    imagefilledpolygon($star, $points, $fillColor);

    // Draw border
    if ($borderSize > 0) {
        imagesetthickness($star, $borderSize);
        imagepolygon($star, $points, $borderColorAlloc);
    }

    return $star;
}

// Function to add text to image
function addText($image, $text, $fontSize, $color, $width, $height)
{
    // Get text dimensions
    $font = 'arialbd.ttf'; // Change the font file if needed
    $bbox = imagettfbbox($fontSize, 0, $font, $text);
    $textWidth = $bbox[2] - $bbox[0];
    $textHeight = $bbox[1] - $bbox[7];

    // Calculate text position
    $x = round(($width - $textWidth) / 2, 0);
    $y = round(($height - $textHeight) / 2 + $textHeight, 0); // Adjust for baseline

    // Add text to image
    $textColor = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
    imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $text);
}

// Function to add text to image with X and Y offset
function addTextWithOffset($image, $text, $fontSize, $color, $width, $height, $xOffset = 0, $yOffset = 0)
{
    // Get text dimensions
    $font = 'arialbd.ttf'; // Change the font file if needed
    $bbox = imagettfbbox($fontSize, 0, $font, $text);
    $textWidth = $bbox[2] - $bbox[0];
    $textHeight = $bbox[1] - $bbox[7];

    // Calculate text position with offset
    $x = round(($width - $textWidth) / 2, 0) + $xOffset;
    $y = round(($height - $textHeight) / 2 + $textHeight, 0) + $yOffset; // Adjust for baseline

    // Add text to image
    $textColor = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
    imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $text);
}

// *************************************************************************
// Enhanced Logging Functions
// *************************************************************************

/**
 * Get client IP address
 * @return string Client IP address
 */
function getClientIP() {
    $ip = 'unknown';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// *************************************************************************
// Icon Cache Management Functions
// *************************************************************************

/**
 * Save generated icon to cache
 * @param resource $image GD image resource
 * @param string $iconFilenameFullPath Full path to cache file
 */
function saveIconToCache($image, $iconFilenameFullPath) {
    global $arrayError;

    if (file_exists(dirname($iconFilenameFullPath))) {
        if (is_writable(dirname($iconFilenameFullPath))) {
            imagepng($image, $iconFilenameFullPath);
            write_string_to_file(LOG_FILE, "Image saved to cache: $iconFilenameFullPath");
        } else {
            $arrayError['CodeDescription'] = "The directory \"" . dirname("$iconFilenameFullPath") . "\" is not writable, set to 777.";
            reportErrorAndExit($arrayError, "n");
        }
    } else {
        $arrayError['CodeDescription'] = "The directory \"" . dirname("$iconFilenameFullPath") . "\" doesn't exist.";
        reportErrorAndExit($arrayError, "n");
    }
}

/**
 * Load icon from cache and serve with proper headers
 * @param string $iconFilenameFullPath Full path to cached icon file
 * @param string $iconFilename Icon filename (used for ETag and error messages)
 * @param bool $cacheHit Whether this was a cache hit
 * @param string $iconType Icon type name (e.g., 'square', 'circle', 'star')
 */
function serveIconFromCache($iconFilenameFullPath, $iconFilename, $cacheHit, $iconType) {
    // Load image from cache to serve
    $im = imagecreatefrompng($iconFilenameFullPath);

    if (!$im) {
        // Create a blank error image if loading fails
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        imagestring($im, 1, 5, 5, 'Error loading ' . $iconFilename, $tc);
    }

    // Generate ETag based on filename (which contains all parameters)
    $etag = md5($iconFilename);

    // Set response headers
    header('Content-Type: image/png');
    header('Access-Control-Allow-Origin: *'); // CORS support
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Cache-Control: public, max-age=31536000, immutable'); // Cache for 1 year
    header('ETag: "' . $etag . '"');
    header('X-Cache-Status: ' . ($cacheHit ? 'HIT' : 'MISS'));
    header('X-Icon-Type: ' . $iconType);

    // Output the image
    imagealphablending($im, false);
    imagesavealpha($im, true);
    imagepng($im);
    imagedestroy($im);
}
