<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

namespace Phacil\Framework;

//use Exception;

/** 
 * @since 1.0.1
 * @package Phacil\Framework 
 */
class Image {
    /**
     * 
     * @var string
     */
    private $file;

    /**
     * 
     * @var \GdImage|false|void
     */
    private $image;

    /**
     * 
     * @var array
     */
    private $info;

    /**
     * @param string $file 
     * @return void 
     * @throws Exception 
     */
    public function __construct($file = null) {
        if(!extension_loaded('gd')){
            throw new \Phacil\Framework\Exception\RuntimeException("The image function requires GD extension on PHP!");
        }

        if($file){
            if ($this->infoChk($file))
                $this->image = $this->create($file);
            else
                throw new \Phacil\Framework\Exception('Error: Could not load image ' . $file . '!');
        }
    }

    public function setImage($file){
        if ($this->infoChk($file))
            $this->image = $this->create($file);
        else
            throw new \Phacil\Framework\Exception('Error: Could not load image ' . $file . '!');
    }

    /**
     * @param string $file 
     * @param bool $infoFile 
     * @return bool 
     */
    private function infoChk($file, $infoFile = true){
        if (file_exists($file)) {
            $this->file = $file;

            $info = getimagesize($file);

            if($infoFile) {
                $this->info = array(
                    'width' => $info[0],
                    'height' => $info[1],
                    'bits' => $info['bits'],
                    'mime' => $info['mime']
                );
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $image 
     * @return \GdImage|false|void 
     */
    private function create($image) {
        $info = getimagesize($image);
        $mime = $info['mime'];

        if ($mime == 'image/gif') {
            return imagecreatefromgif($image);
        } elseif ($mime == 'image/png') {
            return imagecreatefrompng($image);
        } elseif ($mime == 'image/jpeg') {
            return imagecreatefromjpeg($image);
        }
    }

    /**
     * @param string $file 
     * @param int $quality 
     * @return void 
     */
    public function save($file, $quality = 90) {
        $info = pathinfo($file);

        $extension = strtolower($info['extension']);

        if ($extension == 'jpeg' || $extension == 'jpg') {
            imagejpeg($this->image, $file, $quality);
        } elseif($extension == 'png') {
            imagepng($this->image, $file, 0);
        } elseif($extension == 'gif') {
            imagegif($this->image, $file);
        }

        imagedestroy($this->image);
    }

    /**
     * @param int $width 
     * @param int $height 
     * @return void 
     */
    public function resize($width = 0, $height = 0) {
        if (!$this->info['width'] || !$this->info['height']) {
            return;
        }

        $xpos = 0;
        $ypos = 0;

        $scale = min($width / $this->info['width'], $height / $this->info['height']);

        if ($scale == 1) {
            return;
        }

        $new_width = (int)($this->info['width'] * $scale);
        $new_height = (int)($this->info['height'] * $scale);
        $xpos = (int)(($width - $new_width) / 2);
        $ypos = (int)(($height - $new_height) / 2);

        $image_old = $this->image;
        $this->image = imagecreatetruecolor($width, $height);

        if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
            $background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
            imagecolortransparent($this->image, $background);
        } else {
            $background = imagecolorallocate($this->image, 255, 255, 255);
        }

        imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

        imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);

        $this->info['width']  = $width;
        $this->info['height'] = $height;
    }

    /**
     * @param string $file 
     * @param string $position 
     * @param int $opacity 
     * @return false|void 
     */
    public function watermark($file, $position = 'bottomright', $opacity = 100) {

        if($this->infoChk($file, false))
            $watermark = $this->create($file);
        else
            return false;

        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);

        switch($position) {
            case 'topleft':
                $watermark_pos_x = 0;
                $watermark_pos_y = 0;
                break;
            case 'topright':
                $watermark_pos_x = $this->info['width'] - $watermark_width;
                $watermark_pos_y = 0;
                break;
            case 'bottomleft':
                $watermark_pos_x = 0;
                $watermark_pos_y = $this->info['height'] - $watermark_height;
                break;
            case 'bottomright':
                $watermark_pos_x = $this->info['width'] - $watermark_width;
                $watermark_pos_y = $this->info['height'] - $watermark_height;
                break;
        }

        imagealphablending( $this->image, true );
        imagesavealpha( $this->image, true );

        imagealphablending($watermark, false);
        imagesavealpha($watermark, true);
        $background = imagecolorallocatealpha($watermark, 255, 255, 255, 127);
        imagecolortransparent($watermark, $background);

        //$image = $watermark;
        $opacity = $opacity/100;
        $transparency = 1 - $opacity;
        imagefilter($watermark, IMG_FILTER_COLORIZE, 0,0,0,127*$transparency);
        //imagepng($watermark, DIR_IMAGE."testeWater".md5(rand()).".png");

        //$this->imagecopymerge_alpha($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height, $opacity);
        imagecopy($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);

        imagedestroy($watermark);
    }

    /**
     * @param int $top_x 
     * @param int $top_y 
     * @param int $bottom_x 
     * @param int $bottom_y 
     * @return void 
     */
    public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
        $image_old = $this->image;
        $this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

        imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);

        $this->info['width'] = $bottom_x - $top_x;
        $this->info['height'] = $bottom_y - $top_y;
    }

    /**
     * @param int $degree 
     * @param string $color 
     * @return void 
     */
    public function rotate($degree, $color = 'FFFFFF') {
        $rgb = $this->html2rgb($color);

        $this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

        $this->info['width'] = imagesx($this->image);
        $this->info['height'] = imagesy($this->image);
    }

    /**
     * @param mixed $filter 
     * @return void 
     */
    private function filter($filter) {
        imagefilter($this->image, $filter);
    }

    /**
     * @param string $text 
     * @param int $x 
     * @param int $y 
     * @param int $size 
     * @param string $color 
     * @return void 
     */
    private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
        $rgb = $this->html2rgb($color);

        imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
    }

    /**
     * @param string $file 
     * @param int $x 
     * @param int $y 
     * @param int $opacity 
     * @return void 
     */
    private function merge($file, $x = 0, $y = 0, $opacity = 100) {
        $merge = $this->create($file);

        $merge_width = imagesx($image);
        $merge_height = imagesy($image);

        imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
    }

    /**
     * @param string $color 
     * @return false|(int|float)[] 
     */
    private function html2rgb($color) {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }
}
