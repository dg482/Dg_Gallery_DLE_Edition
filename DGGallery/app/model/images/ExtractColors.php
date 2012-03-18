<?php

class Default_Model_Images_ExtractColors {

    var $SampleWidth = 150;
    var $SampleHeight = 150;
    var $error;

    // This function will return the colors in the given image in descending order of prevalance with the key array being the color hex value, and the count being the percentage
    /*

      srcImage = The location of the source image file you want to extract colors from.

      Count = The number of colors you want to extract

      reduceBrightness = Remove variations caused by brightness in the image

      reduceGradients  = Remove variations caused by gradients in the image

      delta = Amount of gap between color values, a lower value will produce more accurate colors with a lot of depth in each shade. Higher values will include more colors of the image, and group them closer together. DEFAULT is 16 AND IS WELL TESTED

      niceNumbers = Return clean, rounded percentages instead of long percents

     */
    function Extract($srcImage, $count = 10, $reduceBrightness = true, $reduceGradients = true, $delta = 16, $niceNumbers = false) {
        // Make sure the image can be opened, then scale it down as we only want the colors that are more prevalaent.
        if (is_readable($srcImage)) {
            if ($delta > 2) {
                $halfOfDelta = $delta / 2 - 1;
            } else {
                $halfOfDelta = 0;
            }
            $size = GetImageSize($srcImage);
            $scale = 1;
            if ($size[0] > 0)
                $scale = min($this->SampleWidth / $size[0], $this->SampleHeight / $size[1]);
            if ($scale < 1) {
                $width = floor($scale * $size[0]);
                $height = floor($scale * $size[1]);
            } else {
                $width = $size[0];
                $height = $size[1];
            }
            $image_resized = imagecreatetruecolor($width, $height);
            if ($size[2] == 1)
                $image_orig = imagecreatefromgif($srcImage);
            if ($size[2] == 2)
                $image_orig = imagecreatefromjpeg($srcImage);
            if ($size[2] == 3)
                $image_orig = imagecreatefrompng($srcImage);
            imagecopyresampled($image_resized, $image_orig, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
            $im = $image_resized;
            $imgWidth = imagesx($im);
            $imgHeight = imagesy($im);
            $total_pixel_count = 0;
            for ($y = 0; $y < $imgHeight; $y++) {
                for ($x = 0; $x < $imgWidth; $x++) {
                    $total_pixel_count++;
                    $index = imagecolorat($im, $x, $y);
                    $colors = imagecolorsforindex($im, $index);
                    // Now we check for neighboring colors to cut out duplicate colors
                    if ($delta > 1) {
                        $colors['red'] = intval((($colors['red']) + $halfOfDelta) / $delta) * $delta;
                        $colors['green'] = intval((($colors['green']) + $halfOfDelta) / $delta) * $delta;
                        $colors['blue'] = intval((($colors['blue']) + $halfOfDelta) / $delta) * $delta;
                        if ($colors['red'] >= 256) {
                            $colors['red'] = 255;
                        }
                        if ($colors['green'] >= 256) {
                            $colors['green'] = 255;
                        }
                        if ($colors['blue'] >= 256) {
                            $colors['blue'] = 255;
                        }
                    }
                    $hex = substr("0" . dechex($colors['red']), -2) . substr("0" . dechex($colors['green']), -2) . substr("0" . dechex($colors['blue']), -2);
                    if (!isset($hexarray[$hex])) {
                        $hexarray[$hex] = 1;
                    } else {
                        $hexarray[$hex]++;
                    }
                }
            }
            // Reduce color gradients if requested
            if ($reduce_gradients) {
                arsort($hexarray, SORT_NUMERIC);
                $gradients = array();
                foreach ($hexarray as $hex => $num) {
                    if (!isset($gradients[$hex])) {
                        // This is where we check neighbors for a color match based on the delta value, this will cut down on the amount of repeat colors.
                        $new_hex = $this->_near_color($hex, $gradients, $delta);
                        $gradients[$hex] = $new_hex;
                    } else {
                        $new_hex = $gradients[$hex];
                    }
                    if ($hex != $new_hex) {
                        $hexarray[$hex] = 0;
                        $hexarray[$new_hex] += $num;
                    }
                }
            }
            // Remove brightness color variation
            if ($reduce_brightness) {
                arsort($hexarray, SORT_NUMERIC);
                $brightness = array();
                foreach ($hexarray as $hex => $num) {
                    if (!isset($brightness[$hex])) {
                        // This is where we check neighbors for a color match based on the delta value, this will cut down on the amount of repeat colors.
                        $new_hex = $this->_convertN($hex, $brightness, $delta);
                        $brightness[$hex] = $new_hex;
                    } else {
                        $new_hex = $brightness[$hex];
                    }
                    if ($hex != $new_hex) {
                        $hexarray[$hex] = 0;
                        $hexarray[$new_hex] += $num;
                    }
                }
            }
            arsort($hexarray, SORT_NUMERIC);
            // Convert the count of the color pixels into a percentage
            foreach ($hexarray as $key => $value) {
                // Check if number formatting is set to TRUE and return a round percentage if so
                if ($niceNumbers == true) {
                    $hexarray[$key] = round(((float) $value / $total_pixel_count), 2);
                } else {
                    $hexarray[$key] = (float) $value / $total_pixel_count;
                }
            }
            if ($count > 0) {
                $arr = array();
                foreach ($hexarray as $key => $value) {
                    if ($count == 0) {
                        break;
                    }
                    $count--;
                    $arr[$key] = $value;
                }
                return $arr;
            } else {
                return $hexarray;
            }
        } else {
            $this->error = "Image " . $srcImage .
                    " could not be found or read, please make sure the image is present!";
            return false;
        }
    }

    function _convertN($hex, $hexarray, $delta) {
        $lowest = 255;
        $highest = 0;
        $colors['red'] = hexdec(substr($hex, 0, 2));
        $colors['green'] = hexdec(substr($hex, 2, 2));
        $colors['blue'] = hexdec(substr($hex, 4, 2));
        if ($colors['red'] < $lowest) {
            $lowest = $colors['red'];
        }
        if ($colors['green'] < $lowest) {
            $lowest = $colors['green'];
        }
        if ($colors['blue'] < $lowest) {
            $lowest = $colors['blue'];
        }
        if ($colors['red'] > $highest) {
            $highest = $colors['red'];
        }
        if ($colors['green'] > $highest) {
            $highest = $colors['green'];
        }
        if ($colors['blue'] > $highest) {
            $highest = $colors['blue'];
        }
        // Only normalize low delta blacks whites and greys
        if ($lowest == $highest) {
            if ($delta <= 32) {
                if ($lowest == 0 || $highest >= (255 - $delta)) {
                    return $hex;
                }
            } else {
                return $hex;
            }
        }
        for (; $highest < 256; $lowest += $delta, $highest += $delta) {
            $new_hex = substr("0" . dechex($colors['red'] - $lowest), -2) . substr("0" .
                            dechex($colors['green'] - $lowest), -2) . substr("0" . dechex($colors['blue'] -
                                    $lowest), -2);
            if (isset($hexarray[$new_hex])) {
                return $new_hex;
            }
        }
        return $hex;
    }

    // Using color RGB values, determine how close / far the colors are from eachother
    function _near_color($hex, $gradients, $delta) {
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));
        // CHECK RED VALUE
        if ($red > $delta) {
            $new_hex = substr("0" . dechex($red - $delta), -2) . substr("0" . dechex($green), -2) . substr("0" . dechex($blue), -2);
            if (isset($gradients[$new_hex])) {
                return $gradients[$new_hex];
            }
        }
        // CHECK GREEN VALUE
        if ($green > $delta) {
            $new_hex = substr("0" . dechex($red), -2) . substr("0" . dechex($green - $delta), -2) . substr("0" . dechex($blue), -2);
            if (isset($gradients[$new_hex])) {
                return $gradients[$new_hex];
            }
        }
        // CHECK BLUE VALUE
        if ($blue > $delta) {
            $new_hex = substr("0" . dechex($red), -2) . substr("0" . dechex($green), -2) .
                    substr("0" . dechex($blue - $delta), -2);
            if (isset($gradients[$new_hex])) {
                return $gradients[$new_hex];
            }
        }
        // CHECK LOWER RED
        if ($red < (255 - $delta)) {
            $new_hex = substr("0" . dechex($red + $delta), -2) . substr("0" . dechex($green), -2) . substr("0" . dechex($blue), -2);
            if (isset($gradients[$new_hex])) {
                return $gradients[$new_hex];
            }
        }
        // CHECK LOWER GREEN
        if ($green < (255 - $delta)) {
            $new_hex = substr("0" . dechex($red), -2) . substr("0" . dechex($green + $delta), -2) . substr("0" . dechex($blue), -2);
            if (isset($gradients[$new_hex])) {
                return $gradients[$new_hex];
            }
        }
        // CHECK LOWER BLUE
        if ($blue < (255 - $delta)) {
            $new_hex = substr("0" . dechex($red), -2) . substr("0" . dechex($green), -2) .
                    substr("0" . dechex($blue + $delta), -2);
            if (isset($gradients[$new_hex])) {
                return $gradients[$new_hex];
            }
        }
        return $hex;
    }

}

