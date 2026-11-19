<?php
$png = imagecreatefrompng("/app/public/images/logo-rodagest.png");
$width = imagesx($png);
$height = imagesy($png);
$transparentCount = 0;
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($png, $x, $y);
        $a = $rgb & 0xFF;
        if ($a === 0) $transparentCount++;
    }
}
echo "Dimensiones: {$width}x{$height}\n";
echo "Pixeles transparentes: {$transparentCount}\n";
imagedestroy($png);
?>