<?php
// https://coursesweb.net/php-mysql/

// Create a 160x80 image
$width = 160;
$height = 80;
$im = imagecreatetruecolor($width, $height);

// sets a color for background
$red = imagecolorallocate($im, 80, 120, 250);
imagefill($im, 0, 0, $red);

// sets and draw a green rectangle in left half
$white = imagecolorallocate($im, 0, 245, 1);
imagefilledrectangle($im, 0, 0, $width/2, $height, $white);

// sets and adds a red text
$text = 'CoursesWeb.net';
$text_color = imagecolorallocate($im, 225, 0, 1);
imagestring($im, 5, 12, $height/3, $text, $text_color);

// Saves the image in 'imgs' folder
imagepng($im, 'addons/php-mysql/image.png');
?>
