<?php 

ini_set("display_erros", 0);

$utilize_cache = (defined('CACHE_IMG'))? CACHE_IMG : false;	// active cache store and view. Optimize processor use
$cache_path    = "css-cache"; // where to store the generated re-sized images. Specify from your document root!
$Imagick_compress = (defined('USE_IMAGICK'))? USE_IMAGICK : false;	// use Imagick PHP module. If false use GD.
$PNG_compress_imagick  = (defined('CACHE_PNG_COMPRESS'))? CACHE_PNG_COMPRESS*10 : 99;	//Level of compress PNG file. Values of 0-100 also 100 is a hightest value of compression (and uses more CPU to create).
$PNG_compress_GD = (defined('CACHE_PNG_COMPRESS'))? CACHE_PNG_COMPRESS : 9;	//Level of compress PNG file. Values of 0-9 also 9 is a hightest value of compression (and uses more CPU to create).
$JPG_quality   = (defined('CACHE_JPG_COMPRESS'))? CACHE_JPG_COMPRESS : 76; //Level of quality JPEG file. Values of 0-100 also 100 is a hightest value of quality and 0 is low quality.
$cache_days    = (defined('CACHE_DAYS'))? CACHE_DAYS : 14;		//Days of image in disk cache storage, after is re-created new file in cache.
$reset_cache   = (defined('RESET_CACHE'))? RESET_CACHE : false;	//reset cache. Also aplicable in active.php?reset=true access browser.
$browser_cache = 60*60*24*7; // How long the BROWSER cache should last (seconds, minutes, hours, days. 7days by default)


session_start();
if(isset($_SESSION['reset'])) {
	$reset_cache = ($_SESSION['reset'] == 'true') ? true : false;
}

//$document_root  = str_replace('system/ecompress', '', __DIR__);
/*$document_root  = $_SERVER['DOCUMENT_ROOT'];
$requested_uri  = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
$requested_file = basename($requested_uri);
$source_file    = $document_root.$requested_uri;*/

$document_root  = (defined('DIR_CACHE'))? substr(DIR_CACHE, 0, -1) : $_SERVER['DOCUMENT_ROOT'];
$requested_uri  = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
$requested_file = basename($requested_uri);
$source_file    = (defined('DIR_PUBLIC'))? substr(DIR_PUBLIC, 0, -1).$requested_uri : $document_root.$requested_uri;

//var_dump($source_file);


$extension = strtolower(pathinfo($source_file, PATHINFO_EXTENSION));

$cache_file = $document_root."/$cache_path/$extension".$requested_uri;

if (in_array($extension, array('png', 'jpeg'))) {
    header("Content-Type: image/".$extension);
  } else {
    header("Content-Type: image/jpeg");
  }
  
  header("Cache-Control: private, max-age=".$browser_cache);
  header('Expires: '.gmdate('D, d M Y H:i:s', time()+$browser_cache).' GMT');
  //header('Content-Length: '.filesize($filename));

if (file_exists($cache_file)) {
	$buffer2 = true;
	$fil = "1";
	$date_cache_file = filemtime($cache_file);
	
}  else {
	$buffer2=false;
	//$buffer = file_get_contents($source_file);
}

if($buffer2 != false and $date_cache_file > strtotime("-$cache_days days") and $utilize_cache == true and $reset_cache == false) {
	header('Content-Length: '.filesize($cache_file));
	readfile($cache_file);
	
} else {

	if($utilize_cache == true) {
		if (!is_dir("$document_root/$cache_path")) { // no
		  if (!mkdir("$document_root/$cache_path", 0755, true)) { // so make it
			if (!is_dir("$document_root/$cache_path")) { // check again to protect against race conditions
			  // uh-oh, failed to make that directory
			  echo("Failed to create cache directory at: $document_root/$cache_path");
			}
		  }
		}

		if (!is_dir("$document_root/$cache_path/$extension")) { // no
		  if (!mkdir("$document_root/$cache_path/$extension", 0755, true)) { // so make it
			if (!is_dir("$document_root/$cache_path/$extension")) { // check again to protect against race conditions
			  // uh-oh, failed to make that directory
			  echo("Failed to create cache directory at: $document_root/$cache_path");
			}
		  }
		}
	}

	if($utilize_cache == true) {

		$parts = explode('/', $cache_file);
			$file = array_pop($parts);
			$dir = '';
			foreach($parts as $part) {
				if(!is_dir($dir .= "/$part")) mkdir($dir, 0755, true);
			}
			//file_put_contents($cache_file, $buffer);
	}

	if (in_array($extension, array('png'))) {

		if($utilize_cache == true) {
			if($Imagick_compress == true) {
				$image = new Imagick($source_file);
				$image->setImageFormat("png");
				$image->setImageCompression(Imagick::COMPRESSION_UNDEFINED);
				$image->setImageCompressionQuality($PNG_compress_imagick);
				$image->stripImage();
				$image->writeImage($cache_file);
				header('Content-Length: '.filesize($cache_file));
				echo $image;
			} else {
				$imagemPNG = imagecreatefrompng($source_file) or die("Não foi possível inicializar uma nova imagem");
				imagealphablending($imagemPNG, false);
				imagesavealpha($imagemPNG, true);
				imagepng($imagemPNG, $cache_file, $PNG_compress_GD);
				header('Content-Length: '.filesize($cache_file));
				readfile($cache_file);
			}
		} else {
			if($Imagick_compress == true) {
				$image = new Imagick($source_file);
				//$image->setImageFormat("png");
				$image->setImageCompression(Imagick::COMPRESSION_UNDEFINED);
				$image->setImageCompressionQuality($PNG_compress_imagick);
				$image->stripImage();
				echo $image;
			} else {
				$imagemPNG = imagecreatefrompng($source_file) or die("Não foi possível inicializar uma nova imagem");
				imagealphablending($imagemPNG, false);
				imagesavealpha($imagemPNG, true);
				imagepng($imagemPNG, NULL, $PNG_compress_GD);
			}
		}

		// Liberar memória
		imagedestroy($imagemPNG);
	}

	if (in_array($extension, array('jpg', 'jpeg'))) {
		if($utilize_cache == true) {
			if($Imagick_compress == true) {
				$image = new Imagick($source_file);
				//$image->setImageFormat("jpeg");
				$image->setImageCompression(Imagick::COMPRESSION_LOSSLESSJPEG);
				$image->setImageCompressionQuality($JPG_quality);
				$image->stripImage();
				$image->writeImage($cache_file);
				header('Content-Length: '.filesize($cache_file));
				echo $image;
			} else {
				$imagemJPG = imagecreatefromjpeg($source_file) or die("Não foi possível inicializar uma nova imagem");
				imagejpeg($imagemJPG, $cache_file, $JPG_quality);
				header('Content-Length: '.filesize($cache_file));
				readfile($cache_file);
			}
		} else {
			if($Imagick_compress == true) {
				$image = new Imagick($source_file);
				//$image->setImageFormat("jpeg");
				$image->setImageCompression(Imagick::COMPRESSION_LOSSLESSJPEG);
				$image->setImageCompressionQuality($JPG_quality);
				$image->stripImage();
				echo $image;
			} else {
				$imagemJPG = imagecreatefromjpeg($source_file) or die("Não foi possível inicializar uma nova imagem");
				imagejpeg($imagemJPG, NULL, $JPG_quality);
			}
		}

		// Liberar memória
		imagedestroy($imagemJPG);
	}
	
}
	
	