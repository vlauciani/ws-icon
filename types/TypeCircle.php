<?php
/**
 * TypeCircle.php - Circle icon generator
 * Handles both 'event1' (deprecated) and 'circle' types
 */

// Validate and get parameters
function validateCircleParams($params) {
    $errors = [];

    // Required: xsize (radius)
    if (!isset($params['xsize'])) {
        $errors[] = [
            'parameter' => 'xsize',
            'message' => 'Parameter "xsize" is required',
            'expected' => 'integer between 10 and 500 (radius in pixels)'
        ];
    } else {
        $xSize = intval($params['xsize']);
        if ($xSize < 10 || $xSize > 500) {
            $errors[] = [
                'parameter' => 'xsize',
                'message' => 'Parameter "xsize" out of range',
                'expected' => 'integer between 10 and 500',
                'value' => $params['xsize']
            ];
        }
    }

    // Required: ysize (radius)
    if (!isset($params['ysize'])) {
        $errors[] = [
            'parameter' => 'ysize',
            'message' => 'Parameter "ysize" is required',
            'expected' => 'integer between 10 and 500 (radius in pixels)'
        ];
    } else {
        $ySize = intval($params['ysize']);
        if ($ySize < 10 || $ySize > 500) {
            $errors[] = [
                'parameter' => 'ysize',
                'message' => 'Parameter "ysize" out of range',
                'expected' => 'integer between 10 and 500',
                'value' => $params['ysize']
            ];
        }
    }

    // Required: bgcolor
    if (!isset($params['bgcolor'])) {
        $errors[] = [
            'parameter' => 'bgcolor',
            'message' => 'Parameter "bgcolor" is required',
            'expected' => '6-digit hexadecimal color (e.g., FF0000)'
        ];
    } elseif (!isValidHexColor($params['bgcolor'])) {
        $errors[] = [
            'parameter' => 'bgcolor',
            'message' => 'Invalid hex color format',
            'expected' => '6-digit hexadecimal color (e.g., FF0000)',
            'value' => $params['bgcolor']
        ];
    }

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

    // Required: textcolor
    if (!isset($params['textcolor'])) {
        $errors[] = [
            'parameter' => 'textcolor',
            'message' => 'Parameter "textcolor" is required',
            'expected' => '6-digit hexadecimal color (e.g., 000000)'
        ];
    } elseif (!isValidHexColor($params['textcolor'])) {
        $errors[] = [
            'parameter' => 'textcolor',
            'message' => 'Invalid hex color format',
            'expected' => '6-digit hexadecimal color (e.g., 000000)',
            'value' => $params['textcolor']
        ];
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
function getCircleFilename($params) {
    $xSize = intval($params['xsize']);
    $ySize = intval($params['ysize']);
    $bgColor = $params['bgcolor'];
    $label = $params['label'];
    $textColor = $params['textcolor'];
    $fontSize = isset($params['fontsize']) ? intval($params['fontsize']) : DEFAULT_FONTSIZE;

    return "circle__xsize-{$xSize}__ysize-{$ySize}__bgcolor-{$bgColor}__label-{$label}__textcolor-{$textColor}__fontsize-{$fontSize}.png";
}

// Generate circle icon
function generateCircleIcon($params) {
    $bgColor = $params['bgcolor'];
    $labelColor = $params['textcolor'];
    $radius = intval($params['xsize']);
    $fontSize = isset($params['fontsize']) ? intval($params['fontsize']) : DEFAULT_FONTSIZE;

    // Convert hex color codes to RGB
    $bgColorRGB = sscanf($bgColor, "%02x%02x%02x");
    $labelColorRGB = sscanf($labelColor, "%02x%02x%02x");

    // Create circle with background color
    $image = createCircle($radius, [
        'r' => $bgColorRGB[0],
        'g' => $bgColorRGB[1],
        'b' => $bgColorRGB[2]
    ]);

    // Add label text
    if (isset($params['label'])) {
        $label = $params['label'];
        addText($image, $label, $fontSize, [
            'r' => $labelColorRGB[0],
            'g' => $labelColorRGB[1],
            'b' => $labelColorRGB[2]
        ], $radius * 2, $radius * 2);
    }

    return $image;
}
