<?php
/**
 * TypeSquare.php - Square icon generator
 * Handles both 'sta2' (deprecated) and 'square' types
 */

// Validate and get parameters
function validateSquareParams($params) {
    $errors = [];

    // Required: label
    if (!isset($params['label'])) {
        $errors[] = [
            'parameter' => 'label',
            'message' => 'Parameter "label" is required',
            'expected' => 'string (1-10 characters)'
        ];
    } elseif (strlen($params['label']) > 10) {
        $errors[] = [
            'parameter' => 'label',
            'message' => 'Parameter "label" is too long',
            'expected' => 'string (1-10 characters)',
            'value' => $params['label']
        ];
    }

    // Required: labelcolor
    if (!isset($params['labelcolor'])) {
        $errors[] = [
            'parameter' => 'labelcolor',
            'message' => 'Parameter "labelcolor" is required',
            'expected' => '6-digit hexadecimal color (e.g., FF0000)'
        ];
    } elseif (!isValidHexColor($params['labelcolor'])) {
        $errors[] = [
            'parameter' => 'labelcolor',
            'message' => 'Invalid hex color format',
            'expected' => '6-digit hexadecimal color (e.g., FF0000)',
            'value' => $params['labelcolor']
        ];
    }

    // Required: bgcolor
    if (!isset($params['bgcolor']) || $params['bgcolor'] === 'undefined') {
        $errors[] = [
            'parameter' => 'bgcolor',
            'message' => 'Parameter "bgcolor" is required',
            'expected' => '6-digit hexadecimal color (e.g., FFFFFF)'
        ];
    } elseif (!isValidHexColor($params['bgcolor'])) {
        $errors[] = [
            'parameter' => 'bgcolor',
            'message' => 'Invalid hex color format',
            'expected' => '6-digit hexadecimal color (e.g., FFFFFF)',
            'value' => $params['bgcolor']
        ];
    }

    // Required: dbstatuscolor
    if (!isset($params['dbstatuscolor']) || $params['dbstatuscolor'] === 'undefined') {
        $errors[] = [
            'parameter' => 'dbstatuscolor',
            'message' => 'Parameter "dbstatuscolor" is required',
            'expected' => '6-digit hexadecimal color (e.g., 00FF00)'
        ];
    } elseif (!isValidHexColor($params['dbstatuscolor'])) {
        $errors[] = [
            'parameter' => 'dbstatuscolor',
            'message' => 'Invalid hex color format',
            'expected' => '6-digit hexadecimal color (e.g., 00FF00)',
            'value' => $params['dbstatuscolor']
        ];
    }

    // Optional: bordersize (validate if provided)
    if (isset($params['bordersize'])) {
        $borderSize = intval($params['bordersize']);
        if ($borderSize < 0 || $borderSize > 10) {
            $errors[] = [
                'parameter' => 'bordersize',
                'message' => 'Parameter "bordersize" out of range',
                'expected' => 'integer between 0 and 10',
                'value' => $params['bordersize']
            ];
        }
    }

    // Optional: fontsize (validate if provided)
    if (isset($params['fontsize'])) {
        $fontSize = intval($params['fontsize']);
        if ($fontSize < 6 || $fontSize > 72) {
            $errors[] = [
                'parameter' => 'fontsize',
                'message' => 'Parameter "fontsize" out of range',
                'expected' => 'integer between 6 and 72',
                'value' => $params['fontsize']
            ];
        }
    }

    return $errors;
}

// Generate filename based on parameters
function getSquareFilename($params) {
    $label = $params['label'];
    $bgColor = isset($params['bgcolor']) && $params['bgcolor'] !== 'undefined' ? $params['bgcolor'] : 'FFFFFF';
    $labelColor = isset($params['labelcolor']) && $params['labelcolor'] !== 'undefined' ? $params['labelcolor'] : '000000';
    $dbStatusColor = isset($params['dbstatuscolor']) && $params['dbstatuscolor'] !== 'undefined' ? $params['dbstatuscolor'] : '000000';
    $borderSize = isset($params['bordersize']) ? intval($params['bordersize']) : 3;
    $fontSize = isset($params['fontsize']) ? intval($params['fontsize']) : DEFAULT_FONTSIZE;

    return "square__label-{$label}__bgcolor-{$bgColor}__labelcolor-{$labelColor}__dbstatuscolor-{$dbStatusColor}__bordersize-{$borderSize}__fontsize-{$fontSize}.png";
}

// Generate square icon
function generateSquareIcon($params) {
    $width = 50;
    $height = 50;

    $bgColor = isset($params['bgcolor']) && $params['bgcolor'] !== 'undefined' ? $params['bgcolor'] : 'FFFFFF';
    $labelColor = isset($params['labelcolor']) && $params['labelcolor'] !== 'undefined' ? $params['labelcolor'] : '000000';
    $dbStatusColor = isset($params['dbstatuscolor']) && $params['dbstatuscolor'] !== 'undefined' ? $params['dbstatuscolor'] : '000000';
    $borderSize = isset($params['bordersize']) ? intval($params['bordersize']) : 3;
    $fontSize = isset($params['fontsize']) ? intval($params['fontsize']) : DEFAULT_FONTSIZE;

    // Convert hex color codes to RGB
    $bgColorRGB = sscanf($bgColor, "%02x%02x%02x");
    $labelColorRGB = sscanf($labelColor, "%02x%02x%02x");
    $dbStatusColorRGB = sscanf($dbStatusColor, "%02x%02x%02x");

    // Create image
    $image = imagecreatetruecolor($width, $height);

    // Create square with background color and border
    $square = createSquare(
        $image,
        $width,
        $height,
        [
            'r' => $bgColorRGB[0],
            'g' => $bgColorRGB[1],
            'b' => $bgColorRGB[2]
        ],
        $borderSize,
        [
            'r' => $dbStatusColorRGB[0],
            'g' => $dbStatusColorRGB[1],
            'b' => $dbStatusColorRGB[2]
        ]
    );
    imagecopy($image, $square, 0, 0, 0, 0, $width, $height);

    // Add label text
    if (isset($params['label'])) {
        $label = $params['label'];
        addText($image, $label, $fontSize, [
            'r' => $labelColorRGB[0],
            'g' => $labelColorRGB[1],
            'b' => $labelColorRGB[2]
        ], $width, $height);
    }

    return $image;
}
