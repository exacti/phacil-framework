<?php
/**
 * CSS and JS Compression
 * Copyright (c) 2014 ExacTI IT Solutions.
 * Creative Commons Attribution, Share-Alike.
 *
 *
 * To use this in your HTML, link to it in the usual way:
 * <link rel="stylesheet" type="text/css" media="screen, print, projection" href="/css/compressed.css.php" />
 */

/* Add your CSS files to this array (THESE ARE ONLY EXAMPLES) */

ini_set("display_erros", 0);

$utilize_cache = (defined('CACHE_JS_CSS'))? CACHE_JS_CSS : false;
$cache_path    = "css-cache"; // where to store the generated re-sized images. Specify from your document root!
$use_js_plus   = (defined('CACHE_use_js_plus'))? CACHE_use_js_plus : false;
$cache_days    = (defined('CACHE_DAYS'))? CACHE_DAYS : 14;
$reset_cache   = (defined('RESET_CACHE'))? RESET_CACHE : false;
$minify		   = (defined('CACHE_MINIFY')) ? CACHE_MINIFY : false;
$include	= false;

session_start();
if(isset($_SESSION['reset'])) {
	$reset_cache = ($_SESSION['reset'] == 'true') ? true : false;
}

if($include == true) {
	$document_root  = (defined('DIR_CACHE'))? substr(DIR_CACHE, 0, -1) : $_SERVER['DOCUMENT_ROOT'];
	$requested_uri  = parse_url(urldecode($URI), PHP_URL_PATH);
	$requested_file = basename($requested_uri);
	$source_file    = $URI;

} else {
	$document_root  = (defined('DIR_CACHE'))? substr(DIR_CACHE, 0, -1) : $_SERVER['DOCUMENT_ROOT'];
	$requested_uri  = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
	$requested_file = basename($requested_uri);
	$source_file    = (defined('DIR_PUBLIC'))? substr(DIR_PUBLIC, 0, -1).$requested_uri : $document_root.$requested_uri;
}

$ignoreFiles = array(
  "TreeMenu_v25.js"
);

/**
 * Ideally, you wouldn't need to change any code beyond this point.
 */
 


$extension = strtolower(pathinfo($source_file, PATHINFO_EXTENSION));

$cache_file = $document_root."/$cache_path/$extension".$requested_uri;



if (file_exists($cache_file) and $utilize_cache === true) {
	$buffer2 = file_get_contents($cache_file);
	$fil = "1";
	$date_cache_file = filemtime($cache_file);


}  else {
	$buffer2=false;
	$buffer = file_get_contents($source_file);
}


if($buffer2 != false and $date_cache_file > strtotime("-$cache_days days") and $utilize_cache == true and $reset_cache == false) {
	    $buffer = $buffer2;
	} 
	else {

	    if(!isset($buffer)) {
            $buffer = file_get_contents($source_file);
        }

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
        /*foreach ($cssFiles as $cssFile) {
          $buffer .= file_get_contents($cssFile);
        }*/

        if (in_array($extension, array('css')) && $minify == true) {
            // Remove comments
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

            // Remove space after colons
            $buffer = str_replace(': ', ':', $buffer);

            //$buffer = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $buffer);

            $buffer = preg_replace('/<!--(.*)-->/Uis', '', $buffer);


            // Remove whitespace
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

        } elseif($minify == true) {

            $buffer = preg_replace('/<!--(.*)-->/Uis', '', $buffer);
            if($use_js_plus == true) {
                include_once __DIR__.'/JSMinPlus.php';
                $buffer = JSMinPlus::minify($buffer);
            } else {
                include_once __DIR__.'/JSMin.php';
                $buffer = JSMin::minify($buffer);
            }

        }

        if (in_array($requested_file, $ignoreFiles)) {
            $buffer = file_get_contents($source_file);
        }

        //echo $cache_file;

        if($utilize_cache == true) {
            $parts = explode('/', $cache_file);
            $file = array_pop($parts);
            $dir = '';
            foreach($parts as $part) {
                if(!is_dir($dir .= "/$part")) mkdir($dir, 0755, true);
            }
            file_put_contents($cache_file, $buffer);
        }
 
}

if($include == true) {
	
	$caminho = str_replace(array($basefolder, $requested_file), '', $source_file);
	
	$caminho = explode("/", $caminho);
	
	$r = count($caminho) - 3;
	
	for($i = 0; $i <= $r; $i++) {
		//echo $i;
		$caminho2 .= $caminho[$i]."/";
	}
	
	//echo $caminho2;
	
	//$caminho = str_replace(array($basefolder, $requested_file, end($caminho)), '', $source_file);
	
	if (in_array($extension, array('css'))) {
		$buffer = '<style>'.str_replace(array("../../../","../../", "../"), "/".$prev_u.$caminho2, $buffer).'</style>';
	} else {
		$buffer = '<script>'.$buffer.'</script>';
	}
	
} else {
    // Enable caching
    header('Cache-Control: public');

    // Enable GZip encoding.
    ob_start("ob_gzhandler");

    // Expire in one day
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
    //header('Accept-Encoding: deflate');
    //header('Content-Encoding: deflate');

    // Set the correct MIME type, because Apache won't set it for us

      if (in_array($extension, array('css'))) {
        header("Content-Type: text/".$extension);
      } else {
        header("Content-Type: text/javascript");
      }

}
// Write everything out
echo $buffer;

