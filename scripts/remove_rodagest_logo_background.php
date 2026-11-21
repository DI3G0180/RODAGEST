<?php

declare(strict_types=1);

$project = dirname(__DIR__);
$target = $project.'/public/images/logo-rodagest.png';
$source = is_file($target) ? $target : $project.'/public/logo-rodagest.png';
$backup = $project.'/storage/app/private/logo-rodagest-original.png';
$temporary = $target.'.tmp.png';

if (!extension_loaded('gd')) {
    fwrite(STDERR, "PHP GD is required.\n");
    exit(1);
}

$image = imagecreatefrompng($source);
if (!$image) {
    fwrite(STDERR, "Could not open the RODAGEST PNG.\n");
    exit(1);
}

$width = imagesx($image);
$height = imagesy($image);
$samples = [];
foreach ([[1, 1], [$width - 2, 1], [1, $height - 2], [$width - 2, $height - 2]] as [$x, $y]) {
    $rgb = imagecolorsforindex($image, imagecolorat($image, $x, $y));
    $samples[] = [$rgb['red'], $rgb['green'], $rgb['blue']];
}
$background = [];
for ($channel = 0; $channel < 3; $channel++) {
    $background[$channel] = (int) round(array_sum(array_column($samples, $channel)) / count($samples));
}

$privateDirectory = dirname($backup);
if (!is_dir($privateDirectory)) mkdir($privateDirectory, 0775, true);
if (!is_file($backup) && !copy($source, $backup)) {
    fwrite(STDERR, "Could not preserve the original logo.\n");
    exit(1);
}

if (!is_dir(dirname($target))) mkdir(dirname($target), 0775, true);
imagealphablending($image, false);
imagesavealpha($image, true);

for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
        $r = $color['red'];
        $g = $color['green'];
        $b = $color['blue'];

        // Remove warm cream from the former artwork and the neutral gray/white
        // checkerboard that had been flattened into the replacement PNG.
        $warmBackground = min($r, $g, $b) > 160 && $r >= $g - 8 && $g >= $b - 12 && ($r - $b) > 16;
        $checkerBackground = min($r, $g, $b) > 178 && (max($r, $g, $b) - min($r, $g, $b)) < 46;
        if (!$warmBackground && !$checkerBackground) continue;

        $distance = max(
            abs($r - $background[0]),
            abs($g - $background[1]),
            abs($b - $background[2])
        );
        $alpha = $distance <= 28 ? 127 : ($distance >= 138 ? 0 : (int) round(127 * (138 - $distance) / 110));
        if ($checkerBackground && !$warmBackground) $alpha = 127;

        $transparentColor = imagecolorallocatealpha($image, $r, $g, $b, $alpha);
        imagesetpixel($image, $x, $y, $transparentColor);
    }
}

if (!imagepng($image, $temporary, 9)) {
    imagedestroy($image);
    fwrite(STDERR, "Could not write the transparent PNG.\n");
    exit(1);
}
imagedestroy($image);

if (!rename($temporary, $target)) {
    @unlink($temporary);
    fwrite(STDERR, "Could not replace the logo PNG.\n");
    exit(1);
}

echo "Transparent logo generated; original preserved privately.\n";
