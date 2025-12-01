<?php
/**
 * TypeHexagon.php - Hexagon icon generator
 */

// Validate and get parameters
function validateHexagonParams($params) {
    $errors = [];

    // Required: bgcolor
    if (!isset($params['bgcolor']) || $params['bgcolor'] === 'undefined') {
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

    // Optional: label (validate length if provided)
    if (isset($params['label']) && !empty($params['label']) && strlen($params['label']) > 10) {
        $errors[] = [
            'parameter' => 'label',
            'message' => 'Parameter "label" is too long',
            'expected' => 'string (1-10 characters)',
            'value' => $params['label']
        ];
    }

    // Optional: labelcolor (validate if provided)
    if (isset($params['labelcolor']) && $params['labelcolor'] !== 'undefined' && !isValidHexColor($params['labelcolor'])) {
        $errors[] = [
            'parameter' => 'labelcolor',
            'message' => 'Invalid hex color format',
            'expected' => '6-digit hexadecimal color (e.g., FFFFFF)',
            'value' => $params['labelcolor']
        ];
    }

    // Required: bordercolor
    if (!isset($params['bordercolor']) || $params['bordercolor'] === 'undefined') {
        $errors[] = [
            'parameter' => 'bordercolor',
            'message' => 'Parameter "bordercolor" is required',
            'expected' => '6-digit hexadecimal color (e.g., 000000)'
        ];
    } elseif (!isValidHexColor($params['bordercolor'])) {
        $errors[] = [
            'parameter' => 'bordercolor',
            'message' => 'Invalid hex color format',
            'expected' => '6-digit hexadecimal color (e.g., 000000)',
            'value' => $params['bordercolor']
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
function getHexagonFilename($params) {
    $bgColor = isset($params['bgcolor']) && $params['bgcolor'] !== 'undefined' ? $params['bgcolor'] : 'FFFFFF';
    $label = isset($params['label']) && !empty($params['label']) ? $params['label'] : 'EMPTY';
    $labelColor = isset($params['labelcolor']) && $params['labelcolor'] !== 'undefined' ? $params['labelcolor'] : '000000';
    $borderColor = isset($params['bordercolor']) && $params['bordercolor'] !== 'undefined' ? $params['bordercolor'] : '000000';
    $borderSize = isset($params['bordersize']) ? intval($params['bordersize']) : 3;
    $fontSize = isset($params['fontsize']) ? intval($params['fontsize']) : DEFAULT_FONTSIZE;

    return "hexagon__bgcolor-{$bgColor}__label-{$label}__labelcolor-{$labelColor}__bordercolor-{$borderColor}__bordersize-{$borderSize}__fontsize-{$fontSize}.png";
}

// Generate hexagon icon
function generateHexagonIcon($params) {
    $bgColor = isset($params['bgcolor']) && $params['bgcolor'] !== 'undefined' ? $params['bgcolor'] : 'FFFFFF';
    $labelColor = isset($params['labelcolor']) && $params['labelcolor'] !== 'undefined' ? $params['labelcolor'] : '000000';
    $borderColor = isset($params['bordercolor']) && $params['bordercolor'] !== 'undefined' ? $params['bordercolor'] : '000000';
    $size = 50; // Default hexagon size
    $borderSize = isset($params['bordersize']) ? intval($params['bordersize']) : 3;
    $fontSize = isset($params['fontsize']) ? intval($params['fontsize']) : DEFAULT_FONTSIZE;

    // Convert hex color codes to RGB
    $bgColorRGB = sscanf($bgColor, "%02x%02x%02x");
    $labelColorRGB = sscanf($labelColor, "%02x%02x%02x");
    $borderColorRGB = sscanf($borderColor, "%02x%02x%02x");

    // Create hexagon with background color and border
    $image = createHexagon(
        $size,
        [
            'r' => $bgColorRGB[0],
            'g' => $bgColorRGB[1],
            'b' => $bgColorRGB[2]
        ],
        $borderSize,
        [
            'r' => $borderColorRGB[0],
            'g' => $borderColorRGB[1],
            'b' => $borderColorRGB[2]
        ]
    );

    // Add label text (only if label is provided and not empty)
    if (isset($params['label']) && !empty($params['label'])) {
        $label = $params['label'];
        addText($image, $label, $fontSize, [
            'r' => $labelColorRGB[0],
            'g' => $labelColorRGB[1],
            'b' => $labelColorRGB[2]
        ], $size, $size);
    }

    return $image;
}
