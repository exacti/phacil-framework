<?php

class Captcha {
	protected $code;
	public $height = 40;
    public $width = 220;
    public $numChar = 8;
    protected $iscale = 0.9;
	protected $perturbation = 0.90;
	protected $noise_level = 1;
	protected $background = 'black';
	public $fonts = __DIR__."/fonts/*/*.ttf";
	public $pos = 'ABCDEFGHJKLMNOPQRSTUWVXZ0123456789abcdefhijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUWVXZ0123456789';

	function __construct($width = NULL, $height = NULL, $numChar = 6, $background = 'black') {

        if(!extension_loaded('gd')){
            throw new \Exception("The captcha function requires GD extension on PHP!");
        }

		$this->numChar = $numChar;
		$this->background = $background;

		$pos = str_split($this->pos);


		for($i = 0; $i < $this->numChar; $i++) {
			$this->code[] = $pos[mt_rand(0, (count($pos) -1))];
		}

		
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
		return implode("", $this->code);
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
		$varYellow = imagecolorallocatealpha($image, 255, 255, 0, mt_rand(30,100));
		$varBlue = imagecolorallocatealpha($image, 0, 0, 255, mt_rand(30,100));
		$varBlack = imagecolorallocatealpha($image, 33, 33, 33, mt_rand(85,95));
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

        $this->gdlinecolor = $yellow;
		
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
		
         
		if(mt_rand(0,2) == 2) {
			imagefilledellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $red);
		} else {
			imagefilledrectangle($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(5, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(5, $this->height)), $red);
		}
		if(mt_rand(1,2) == 2) {
			imagefilledellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $green);
		} else {
			imagefilledrectangle($image, ceil(mt_rand(5, 145)), ceil(mt_rand(0, 35)), ceil(mt_rand(5, 175)), ceil(mt_rand(0, 40)), $green);
		}
		if(mt_rand(1,2) == 2) {
			imagefilledellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $varBlue);
		} else {
			imagefilledrectangle($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $varBlue);
		}
		/*if(mt_rand(1,2) == 2) {
			imagefilledellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $orange);
		} else {
			imagefilledrectangle($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $orange);
		}*/
		if(mt_rand(1,2) == 2) {
			imagefilledellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $yellow);
		} else {
			imagefilledrectangle($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $yellow);
		}


        imagefilledrectangle($image, 0, 0, $width, 0, $black);
        imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black);
        imagefilledrectangle($image, 0, 0, 0, $height - 1, $black);
        imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black);


        $this->ttfFonts = glob($this->fonts);
         
        $space = ($this->width - 10) / $this->numChar;

		foreach($this->code as $key => $character) {
            //$qualfonte = __DIR__."/gd_fonts/".mt_rand(0,8).".gdf";

            $qualfonte = mt_rand(0, (count($this->ttfFonts)-1));
            $fonteCaptcha = $this->ttfFonts[$key];

            $tamanhoFonte = mt_rand(16, 26);

            //Carregar uma nova fonte
            //$fonteCaptcha = imageloadfont($qualfonte);

			$y = ceil(mt_rand(($tamanhoFonte+5), $this->height-5 ));
			$divisor = 1;
			$plus = 10;
			$incre = 0;
			//$securityX = (isset($x)) ? $x + 18 : 0;
			switch ($key) {
				case "0":
					$x = ceil(mt_rand(0, $space-$plus));
					break;
				case "1":
					$x = ceil(mt_rand($x+$incre/$divisor+$plus, $space*2));
					break;
				case "2":
					$x = ceil(mt_rand($x+$incre/$divisor+$plus, $space*3));
					break;
				case "3":
					$x = ceil(mt_rand($x+$incre/$divisor+$plus, $space*4));
					break;
				case "4":
					$x = ceil(mt_rand($x+$incre/$divisor+$plus, $space*5));
					break;
				case "5":
					$x = ceil(mt_rand($x+$incre/$divisor+$plus, $space*5+$space/2));
					break;
				default:
					$x = ceil(mt_rand($x+$incre/$divisor+$plus, $space*$key+$space/2));
					break;
			}

			if(isset($securityX) && $x < $securityX) {
                $x = $securityX;
			}

			$rotate = mt_rand(-20, 35);


			//imagechar (  $image , $fonteCaptcha , $x , $y, $character , $fontColors[mt_rand(0,count($fontColors)-1)]);
            $dadosChar = imagettftext ( $image , $tamanhoFonte, $rotate , $x ,  $y ,  $fontColors[mt_rand(0,count($fontColors)-1)], $fonteCaptcha , $character );

            if(mt_rand(0, 5) == 1)
                $dadosChar = imagettftext ( $image , $tamanhoFonte, $rotate , $x+1 ,  $y+1 ,  $fontColors[mt_rand(0,count($fontColors)-1)], $fonteCaptcha , $character );

			$securityX = max([$dadosChar[2], $dadosChar[4]]);

		}


        $image = $this->drawNoise($image);

		//imagefilter($image, IMG_FILTER_PIXELATE, 2,1);

		//exit;
		if(mt_rand(1,2) == 2) {
			imageellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $red);
		} else {
			imagerectangle($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $red);
		}
		if(mt_rand(1,2) == 2) {
			imageellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $green);
		} else {
			imageline($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $green);
		}
		if(mt_rand(1,2) == 2) {
			imageellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $blue);
		} else {
			imagerectangle($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $blue);
		}
		if(mt_rand(1,2) == 2) {
			imageellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $orange);
		} else {
			imageline($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $orange);
		}
		if(mt_rand(1,2) == 2) {
			imageellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $varYellow);
		} else {
			imageline($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $varYellow);
		}
		if(mt_rand(1,2) == 2) {
			imageellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $punyWhite);
		} else {
			imageline($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $punyWhite);
		}
		if(mt_rand(1,2) == 2) {
			imagefilledellipse($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), 30, 30, $varBlack); 
		} else {
			imagefilledrectangle($image, ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width)), ceil(mt_rand(0, $this->height)), $varBlack); 
		}
		//imagearc (  $image , ceil(mt_rand(5, $this->width)) , ceil(mt_rand(0, $this->height)) ,ceil(mt_rand(5, $this->width)) , ceil(mt_rand(0, $this->height)), ceil(mt_rand(5, $this->width))/ceil(mt_rand(5, $this->height)) , ceil(mt_rand(5, $this->height))/ceil(mt_rand(0, $this->width)) , $pureYellow );
			
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



    protected function drawNoise($im)
    {
        if ($this->noise_level > 10) {
            $noise_level = 10;
        } else {
            $noise_level = $this->noise_level;
        }
        $t0 = microtime(true);
        $noise_level *= M_LOG2E;
        for ($x = 1; $x < $this->width; $x += 20) {
            for ($y = 1; $y < $this->height; $y += 20) {
                for ($i = 0; $i < $noise_level; ++$i) {
                    $x1 = mt_rand($x, $x + 20);
                    $y1 = mt_rand($y, $y + 20);
                    $size = mt_rand(1, 3);
                    if ($x1 - $size <= 0 && $y1 - $size <= 0) continue; // dont cover 0,0 since it is used by imagedistortedcopy
                    imagefilledarc($im, $x1, $y1, $size, $size, 0, mt_rand(180,360), $this->gdlinecolor, IMG_ARC_PIE);
                }
            }
        }
        $t = microtime(true) - $t0;

        return $im;
        /*
        // DEBUG
        imagestring($this->im, 5, 25, 30, "$t", $this->gdnoisecolor);
        header('content-type: image/png');
        imagepng($this->im);
        exit;
        */
    }

    protected function distortedCopy($im, $im2)
    {
        $this->tmpimg = $im2;
        $this->im = $im;

        $numpoles = 3;       // distortion factor
        $px       = array(); // x coordinates of poles
        $py       = array(); // y coordinates of poles
        $rad      = array(); // radius of distortion from pole
        $amp      = array(); // amplitude
        $x        = ($this->width / 4); // lowest x coordinate of a pole
        $maxX     = $this->width - $x;  // maximum x coordinate of a pole
        $dx       = mt_rand($x / 10, $x);     // horizontal distance between poles
        $y        = mt_rand(20, $this->height - 20);  // random y coord
        $dy       = mt_rand(20, $this->height * 0.7); // y distance
        $minY     = 20;                                     // minimum y coordinate
        $maxY     = $this->height - 20;               // maximum y cooddinate
        // make array of poles AKA attractor points
        for ($i = 0; $i < $numpoles; ++ $i) {
            $px[$i]  = ($x + ($dx * $i)) % $maxX;
            $py[$i]  = ($y + ($dy * $i)) % $maxY + $minY;
            $rad[$i] = mt_rand($this->height * 0.4, $this->height * 0.8);
            $tmp     = ((- $this->frand()) * 0.15) - .15;
            $amp[$i] = $this->perturbation * $tmp;
        }
        $bgCol   = imagecolorat($this->tmpimg, 0, 0);
        $width2  = $this->iscale * $this->width;
        $height2 = $this->iscale * $this->height;
        imagepalettecopy($this->im, $this->tmpimg); // copy palette to final image so text colors come across
        // loop over $img pixels, take pixels from $tmpimg with distortion field
        for ($ix = 0; $ix < $this->width; ++ $ix) {
            for ($iy = 0; $iy < $this->height; ++ $iy) {
                $x = $ix;
                $y = $iy;
                for ($i = 0; $i < $numpoles; ++ $i) {
                    $dx = $ix - $px[$i];
                    $dy = $iy - $py[$i];
                    if ($dx == 0 && $dy == 0) {
                        continue;
                    }
                    $r = sqrt($dx * $dx + $dy * $dy);
                    if ($r > $rad[$i]) {
                        continue;
                    }
                    $rscale = $amp[$i] * sin(3.14 * $r / $rad[$i]);
                    $x += $dx * $rscale;
                    $y += $dy * $rscale;
                }
                $c = $bgCol;
                $x *= $this->iscale;
                $y *= $this->iscale;
                if ($x >= 0 && $x < $width2 && $y >= 0 && $y < $height2) {
                    $c = imagecolorat($this->tmpimg, $x, $y);
                }
                if ($c != $bgCol) { // only copy pixels of letters to preserve any background image
                    imagesetpixel($this->im, $ix, $iy, $c);
                }
            }
        }

        return $this->im;
    }

    protected function frand()
    {
        return 0.0001 * mt_rand(0,9999);
    }
}
