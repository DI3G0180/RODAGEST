<?php
$source = 'public/images/LOGO RodaGestt.jpg';
$destination = 'public/images/logo-rodagest.png';

// Check source exists
if (!file_exists($source)) {
    die("Source file not found: $source\n");
}

// Load the JPG image
$jpg = imagecreatefromjpeg($source);
if (!$jpg) {
    die("Failed to load JPG image\n");
}

// Get image dimensions
$width = imagesx($jpg);
$height = imagesy($jpg);

// Create a new true color image for the PNG with transparency
$png = imagecreatetruecolor($width, $height);

// Make white pixels transparent (R > 235, G > 235, B > 235)
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($jpg, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        // If color is almost white, make it transparent
        if ($r > 235 && $g > 235 && $b > 235) {
            // Set alpha to 0 (transparent)
            imagesetpixel($png, $x, $y, imagecolorallocatealpha($png, 0, 0, 0, 127));
        } else {
            // Otherwise copy the pixel
            $color = imagecolorat($jpg, $x, $y);
            imagesetpixel($png, $x, $y, $color);
        }
    }
}

// Free the original JPG resource
imagedestroy($jpg);

// Save as PNG
if (!imagepng($png, $destination)) {
    die("Failed to save PNG image\n");
}

// Free the PNG resource
imagedestroy($png);

echo "Éxito: Logotipo convertido a $destination\n";
echo "Dimensiones: {$width}x{$height}\n";
?>