<?php

class Captcha {
	protected $code;
	protected $height = 40;
	protected $width = 180;
	protected $numChar = 6;
	protected $background = 'black';

	function __construct($width = NULL, $height = NULL, $numChar = 6, $background = 'black') {

        if(!extension_loaded('gd')){
            throw new \Exception("The captcha function requires GD extension on PHP!");
        }

		$this->numChar = $numChar;
		$this->background = $background;
		$pos = 'ABCDEFGHJKLMNOPQRSTUWVXZ0123456789abcdefhijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUWVXZ0123456789'; 

		for($i = 0; $i < $this->numChar; $i++) {
			$this->code .= substr($pos, mt_rand(0, strlen($pos)-1), 1);
		}
		/*if (function_exists('token')) {
			$this->code = substr(token(100), rand(0, 94), $this->numChar);
		} else {
			$this->code = substr(sha1(mt_rand()), 17, $this->numChar); 
		}*/
		
		$this->width = ($width != NULL) ? $width : $this->width;
		$this->height = ($height != NULL) ? $height : $this->height;
	}

	public function __help() {
	    $helpTxt =
            array(
                "version" => "1.0",
                "Description" => "Captcha module.",
                "Use" => 'Declare new Captcha($width, $height, $numChar, $background) on a variable, use $variable->getCode() to obtain a plain text captcha code (prefer pass thos code to a SESSION) and return $variable->showImage(\'format\').',
                "Formats" => "bmp, jpg, png, wbmp and gif."
            );

	    var_dump($helpTxt, true);
    }

	function getCode(){
		return $this->code;
	}

	function showImage($format = 'png') {
        $image = imagecreatetruecolor($this->width, $this->height);

        $width = imagesx($image); 
        $height = imagesy($image);
		
        $black = imagecolorallocate($image, 33, 33, 33); 
        $white = imagecolorallocate($image, 255, 255, 255); 
        $red = imagecolorallocatealpha($image, 255, 0, 0, 50); 
        $green = imagecolorallocatealpha($image, 0, 255, 0, 75); 
        $blue = imagecolorallocatealpha($image, 0, 0, 255, 50); 
		$orange = imagecolorallocatealpha($image, 255, 136, 0, 50);
		$yellow = imagecolorallocatealpha($image, 255, 255, 0, 50);
		$punyWhite = imagecolorallocatealpha($image, 255, 255, 255, 40); 
		$varYellow = imagecolorallocatealpha($image, 255, 255, 0, rand(30,100));
		$varBlue = imagecolorallocatealpha($image, 0, 0, 255, rand(30,100));
		$varBlack = imagecolorallocatealpha($image, 33, 33, 33, rand(85,95));
		$pureYellow = imagecolorallocate($image, 255, 255, 0); 
		$pureGreen = imagecolorallocate($image, 0, 255, 0);
		$softGreen = imagecolorallocate($image, 153, 241, 197);
		$softBlue = imagecolorallocate($image, 180, 225, 245);
		$softpink = imagecolorallocate($image, 250, 165, 215);
		$pureRed = imagecolorallocate($image, 250, 0, 0);
		$strongGreen = imagecolorallocate($image, 95, 115, 75);
		$pureBlue = imagecolorallocate($image, 0, 0, 215);
		$pureorange = imagecolorallocate($image, 255, 135, 0);
		$strangePurple = imagecolorallocate($image, 0, 80, 90);
		/*$pureBlue = imagecolorallocate($image, 200, 100, 245);*/
		
		switch($this->background) {
			case 'black':
				$fontColors = array($white, $pureYellow, $pureGreen, $softBlue, $softGreen, $softpink);
				imagefilledrectangle($image, 0, 0, $width, $height, $black); 
				break;
			case 'white':
				$fontColors = array($black, $pureRed, $strongGreen, $pureBlue, $pureorange, $strangePurple);
				imagefilledrectangle($image, 0, 0, $width, $height, $white); 
				break;
		}
		
         
		if(rand(0,2) == 2) {
			imagefilledellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $red); 
		} else {
			imagefilledrectangle($image, ceil(rand(5, $this->width)), ceil(rand(5, $this->height)), ceil(rand(5, $this->width)), ceil(rand(5, $this->height)), $red); 
		}
		if(rand(1,2) == 2) {
			imagefilledellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $green); 
		} else {
			imagefilledrectangle($image, ceil(rand(5, 145)), ceil(rand(0, 35)), ceil(rand(5, 175)), ceil(rand(0, 40)), $green); 
		}
		if(rand(1,2) == 2) {
			imagefilledellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $varBlue); 
		} else {
			imagefilledrectangle($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $varBlue); 
		}
		if(rand(1,2) == 2) {
			imagefilledellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $orange); 
		} else {
			imagefilledrectangle($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $orange); 
		}
		if(rand(1,2) == 2) {
			imagefilledellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $yellow); 
		} else {
			imagefilledrectangle($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $yellow); 
		}
		

        imagefilledrectangle($image, 0, 0, $width, 0, $black); 
        imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black); 
        imagefilledrectangle($image, 0, 0, 0, $height - 1, $black); 
        imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black); 
		
		$qualfonte = __DIR__."/18.gdf";
 
		//Carregar uma nova fonte
		$fonteCaptcha = imageloadfont($qualfonte);
         
        //imagestring($image, $fonteCaptcha, intval(($width - (strlen($this->code) * 6)) / 16),  intval(($height - 15) / 4), $this->code, $white);
		
		$txt = str_split($this->code);
		$space = ($this->width-10) / $this->numChar;
				
		foreach($txt as $key => $character) {
			$y = ceil(rand(0,  $this->height - ($this->height - ($this->height -30))));
			$divisor = 1;
			$plus = 10;
			$incre = 0;
			switch ($key) {
				case "0":
					$x = ceil(rand(0, $space-$plus)); 
					break;
				case "1":
					$x = ceil(rand($x+$incre/$divisor+$plus, $space*2)); 
					break;
				case "2":
					$x = ceil(rand($x+$incre/$divisor+$plus, $space*3)); 
					break;
				case "3":
					$x = ceil(rand($x+$incre/$divisor+$plus, $space*4)); 
					break;
				case "4":
					$x = ceil(rand($x+$incre/$divisor+$plus, $space*5));  
					break;
				case "5":
					$x = ceil(rand($x+$incre/$divisor+$plus, $space*5+$space/2)); 
					break;
				default:
					$x = ceil(rand($x+$incre/$divisor+$plus, $space*$key+$space/2));
					break;
			}
			
			imagechar (  $image , $fonteCaptcha , $x , $y, $character , $fontColors[rand(0,count($fontColors)-1)]);
		}
		
		
		if(rand(1,2) == 2) {
			imageellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $red); 
		} else {
			imagerectangle($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $red); 
		}
		if(rand(1,2) == 2) {
			imageellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $green); 
		} else {
			imageline($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $green); 
		}
		if(rand(1,2) == 2) {
			imageellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $blue); 
		} else {
			imagerectangle($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $blue); 
		}
		if(rand(1,2) == 2) {
			imageellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $orange); 
		} else {
			imageline($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $orange); 
		}
		if(rand(1,2) == 2) {
			imageellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $varYellow); 
		} else {
			imageline($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $varYellow); 
		}
		if(rand(1,2) == 2) {
			imageellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $punyWhite); 
		} else {
			imageline($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $punyWhite); 
		}
		if(rand(1,2) == 2) {
			imagefilledellipse($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), 30, 30, $varBlack); 
		} else {
			imagefilledrectangle($image, ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), ceil(rand(5, $this->width)), ceil(rand(0, $this->height)), $varBlack); 
		}
		//imagearc (  $image , ceil(rand(5, $this->width)) , ceil(rand(0, $this->height)) ,ceil(rand(5, $this->width)) , ceil(rand(0, $this->height)), ceil(rand(5, $this->width))/ceil(rand(5, $this->height)) , ceil(rand(5, $this->height))/ceil(rand(0, $this->width)) , $pureYellow );
			
		header('Cache-Control: no-cache');
		
		if($format == 'jpeg' || $format == 'jpg') {
			header('Content-type: image/jpeg');
			imagejpeg($image);
		} elseif ($format == 'png') {
			header('Content-type: image/png');
			imagepng($image);
		} elseif ($format == 'bmp' || $format == 'bitmap') {
			header('Content-type: image/bmp');
			imagebmp($image);
		} elseif ($format == 'gif' || $format == 'giff') {
			header('Content-type: image/gif');
			imagegif($image);
		} elseif ($format == 'wbmp') {
			header('Content-type: image/vnd.wap.wbmp');
			imagewbmp($image);
		}
		
		imagedestroy($image);		
	}
}
?>