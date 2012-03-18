<?php

/**
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.4 (08.2011)
 */
class Default_Model_Images_Gd {

    /**
     * @var
     * int
     * */
    public $setWidth;

    /**
     * @var
     * bool
     * */
    public $copyOriginal;

    /**
     * @var
     * array
     * */
    private $_thumb;

    /**
     * @var
     * array
     * */
    private $_config;

    /**
     * @var
     * string
     * */
    protected $_path;

    /**
     *
     * @var type
     */
    private $_watermark;
    private $_configs;
    public $youTube;
    public $vimeo;

    /**
     * module_images::__construct()
     *
     * @param mixed $path
     * @return void
     */
    public function __construct($path) {
        $this->_path = $path;
        $config = $this->_getConfig();
        $this->_configs = $config;
        if (null === $path && !$config) {
            return;
        }
        $this->_config = array(
            'resize_mode_thumbs' => $config ['resize_mode_original'],
            'watermarkSource' => 'uploads/gallery/assets/',
            'quality' => 90
        );

        $this->_thumb ['mode'] = $this->_config ['resize_mode_thumbs'];
        $this->setWidth = 100;
        $this->setResource($path);
    }

    public function checkSize($watermark = false) {
        $size = explode('x', $this->_configs ['maxprop_original']);
        if (count($size) == 2) {
            if ($this->_thumb ['width'] > $size [0] or $this->_thumb ['height'] > $size [1]) {
                $info = pathinfo($this->_path);
                copy($this->_path, ROOT_DIR . '/uploads/gallery/' . date("Y-m") . '/original/' . $info['basename']);
                $this->cteateThumb($this->_configs ['maxprop_original'], $watermark, $this->_configs ['resize_mode_original']);
                $this->save(true, $this->_path);
                $this->_watermark = true;
            }
        } else {
            if ($this->_thumb ['width'] > $size [0] or $this->_thumb ['height'] > $size [0]) {
                $info = pathinfo($this->_path);
                copy($this->_path, ROOT_DIR . '/uploads/gallery/' . date("Y-m") . '/original/' . $info['basename']);
                $this->cteateThumb($this->_configs ['maxprop_original'], $watermark, $this->_configs ['resize_mode_original']);
                $this->save(true, $this->_path);
                $this->_watermark = true;
            }
        }
        //FIX: доп проверка на наложение
        if ((true != $this->_watermark) && ($watermark)) {
            $this->setWatermarkImage($this->_path);
        }
        $this->clear();
        $this->setResource($this->_path);
    }

    /**
     *
     * @param type $path string
     */
    public function setResource($path) {
        $info = getimagesize($path);
        switch ($info [2]) {
            case 1 :
                $this->_thumb ['format'] = "GIF";
                $this->_thumb ['src'] = @imagecreatefromgif($path);
                break;
            case 2 :
                $this->_thumb ['format'] = "JPEG";
                $this->_thumb ['src'] = imagecreatefromjpeg($path);

                break;
            case 3 :
                $this->_thumb ['format'] = "PNG";
                $this->_thumb ['src'] = imagecreatefrompng($path);
                break;
            default :
                break;
        }
        $this->_thumb ['width'] = imagesx($this->_thumb ['src']);
        $this->_thumb ['height'] = imagesy($this->_thumb ['src']);
    }

    /**
     * module_images::_copyOriginal()
     *
     * @return void
     */
    public function _copyOriginal($copyFile, $destination) {

        copy($copyFile, $destination);
    }

    /**
     * module_images::cteateThumb()
     *
     * @param string $s - size 120x120
     * @param bool $watermark- watermark
     * @return void
     */
    public function cteateThumb($s, $watermark = false, $mode = false) {
        $size = null;
        if (null == $s) {
            $size = array(0 => 800, 1 => $this->_thumb ['height']);
        } else {
            $size = explode('x', $s);
        }

        if (!$this->_thumb) {
            die('ERROR IMAGES');
        }
        $w = $this->_thumb ['width'];
        $h = $this->_thumb ['height'];
        $this->set_width = (!isset($size [1])) ? $size [0] : $size [1];
        if ($mode !== false) { //set thumbs resize
            $this->_thumb ['mode'] = $mode;
        }
        switch ($this->_thumb ['mode']) {
            case 1 :
                $this->_thumb ['scale'] = 'width';
                break;
            case 2 :
                $this->_thumb ['scale'] = 'height';
                break;
            default :
                if ($this->_thumb ['width'] > $this->_thumb ['height']) {
                    $this->_thumb ['scale'] = 'width';
                } else {
                    $this->_thumb ['scale'] = 'height';
                }
                break;
        }
        //FIX: не учитывалось $this->_thumb ['mode'] = 0
        if ($this->_thumb ['mode'] == 0) {
            if ($this->_thumb ['width'] > $this->_thumb ['height']) {
                $this->_thumb ['scale'] = 'width';
            } else {
                $this->_thumb ['scale'] = 'height';
            }
        }
        if (count($size) == 2) { //
            $nw = min($size [0], $w);
            $nh = min($size [1], $h);
            $this->_thumb ['dest'] = imagecreatetruecolor($nw, $nh);
            $w = $this->_thumb['width'];
            $h = $this->_thumb['height'];
            $size_ratio = max($nw / $w, $nh / $h);
            $src_w = ceil($nw / $size_ratio);
            $src_h = ceil($nh / $size_ratio);
            $sx = floor(($w - $src_w) / 2);
            $sy = floor(($h - $src_h) / 2);
            $this->img['des'] = imagecreatetruecolor($nw, $nh);

            if ($this->_thumb ['format'] == "PNG") {
                imagealphablending($this->_thumb ['dest'], false);
                imagesavealpha($this->_thumb ['dest'], true);
            }
            imagecopyresampled($this->_thumb ['dest'], $this->_thumb ['src'], 0, 0, $sx, $sy, $nw, $nh, $src_w, $src_h);
            if ($watermark) {
                $this->insertWaterMark($nw, $nh);
            }
        } else { //
            $this->_thumb ['ratio'] = $this->_thumb [$this->_thumb ['scale']] / $size [0];
            $this->_thumb ['set_width'] = round($w / $this->_thumb ['ratio']);
            $this->_thumb ['set_height'] = round($h / $this->_thumb ['ratio']);
            $this->_thumb ['dest'] = imagecreatetruecolor($this->_thumb ['set_width'], $this->_thumb ['set_height']);
            if ($this->_thumb ['format'] == "PNG") {
                imagealphablending($this->_thumb ['dest'], false);
                imagesavealpha($this->_thumb ['dest'], true);
            }
            imagecopyresampled($this->_thumb ['dest'], $this->_thumb ['src'], 0, 0, 0, 0, $this->_thumb ['set_width'], $this->_thumb ['set_height'], $w, $h);
            if ($watermark) {
                $this->insertWaterMark($this->_thumb ['set_width'], $this->_thumb ['set_height']);
            }
        }
    }

    public function setWatermarkImage($path) {
        if ($this->_watermark) {
            return null;
        }
        $this->setResource($path);
        $this->_thumb ['width'] = imagesx($this->_thumb ['src']);
        $this->_thumb ['height'] = imagesy($this->_thumb ['src']);
        $this->_thumb ['dest'] = $this->_thumb ['src'];
        $this->insertWaterMark($this->_thumb ['width'], $this->_thumb ['height']);
        $this->save(TRUE, $path);
    }

    //TODO: inse?tWaterMark -))
    /**
     * module_images::insertWaterMark()
     *
     * @param mixed $x
     * @param mixed $y
     * @param bool $res
     * @return
     */
    public function insertWaterMark($x, $y, $res = false) {
        $min_image = 80;
        if ($x <= $min_image or $y <= $min_image) {
            return false;
        }
        $watermark_image = $_SERVER ['DOCUMENT_ROOT'] . '/' . $this->_config ['watermarkSource'] . 'watermark_light.png';
        $watermark_image_ = $_SERVER ['DOCUMENT_ROOT'] . '/' . $this->_config ['watermarkSource'] . 'watermark_dark.png';

        if ($this->youTube) {
            $watermark_image = $_SERVER ['DOCUMENT_ROOT'] . '/' . $this->_config ['watermarkSource'] . 'watermark_light_youtube.png';
            $watermark_image_ = $_SERVER ['DOCUMENT_ROOT'] . '/' . $this->_config ['watermarkSource'] . 'watermark_dark_youtube.png';
        }
        if ($this->vimeo) {
            $watermark_image = $_SERVER ['DOCUMENT_ROOT'] . '/' . $this->_config ['watermarkSource'] . 'watermark_light_vimeo.png';
            $watermark_image_ = $_SERVER ['DOCUMENT_ROOT'] . '/' . $this->_config ['watermarkSource'] . 'watermark_dark_vimeo.png';
        }

        if (!file_exists($watermark_image)) {
            //   return;
        }
        $watermark_s = getimagesize($watermark_image);
        if ($this->_thumb ['format'] == "PNG") {
            imagealphablending($this->_thumb ['dest'], false);
            imagesavealpha($this->_thumb ['dest'], true);
        }
        if ($watermark_s) {
            $w_x = ($x - 10 - $watermark_s [0]);
            $w_y = ($y - 10 - $watermark_s [1]);
            $test = imagecreatetruecolor(1, 1);
            imagecopyresampled($test, $this->_thumb ['dest'], 0, 0, $w_x, $w_y, 1, 1, $watermark_s [0], $watermark_s [1]);
            $rgb = imagecolorat($test, 0, 0);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $max = min($r, $g, $b);
            $min = max($r, $g, $b);
            $lightness = (double) (($max + $min) / 510.0);
            imagedestroy($test);
            $watermark_image = ($lightness < 0.5) ? $watermark_image : $watermark_image_;
            $watermark = imagecreatefrompng($watermark_image);
            imagealphablending($this->_thumb ['dest'], true);
            imagealphablending($watermark, true);
            imagecopy($this->_thumb ['dest'], $watermark, $w_x, $w_y, 0, 0, $watermark_s [0], $watermark_s [1]);
            imagedestroy($watermark);
        }
    }

    /**
     * module_images::save()
     *
     * @param bool $res - imagedestroy
     * @param string $save -  path new file
     * @return
     */
    public function save($res = false, $save = "") {
        if (!$this->_thumb ['dest']) {
            $this->_thumb ['dest'] = $this->_thumb ['src'];
        }
        # = $this->_thumb['dest'];
        switch ($this->_thumb ['format']) {
            case 'JPEG' :
                imagejpeg($this->_thumb ['dest'], $save, $this->_config ['quality']);
                break;
            case 'PNG' :
                imagealphablending($this->_thumb ['dest'], false);
                imagesavealpha($this->_thumb ['dest'], true);
                imagepng($this->_thumb ['dest'], $save);
                break;
            case 'GIF' :
                imagegif($this->_thumb ['dest'], $save);
                break;
        }
        if ($res) {
            $this->clear();
        }

        #$this->_thumb = null;
    }

    public function getWidth() {
        return (int) $this->_thumb ['width'];
    }

    public function getHeight() {
        return (int) $this->_thumb ['height'];
    }

    public function extractColors($file, $n = 10) {

        $extractor = new Default_Model_Images_ExtractColors ();
        $_colors = $extractor->Extract($file, $n);
        $mp = 0;
        foreach ($_colors as $color => $count) {
            $mp += $count;
        }
        $procent = $mp / 100;
        foreach ($_colors as $color => $count) {
            $_colors[$color] = round(($count / $procent), 4);
        }
        return $_colors;
    }

    public function clear() {
        if (is_resource($this->_thumb ['src']))
            imagedestroy($this->_thumb ['src']);
        if (is_resource($this->_thumb ['dest']))
            imagedestroy($this->_thumb ['dest']);
    }

    private function _getConfig() {
        if (file_exists(ROOT_DIR . '/DGGallery/app/config/config_gallery.php')) {
            return require ROOT_DIR . '/DGGallery/app/config/config_gallery.php';
        } else {
            return null;
        }
    }

}

