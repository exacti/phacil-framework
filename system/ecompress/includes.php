<?php



//$arquivos = array('/exacti5/bootstrap/css/bootstrap.min.css', '/exacti5/bootstrap/responsive-tables.js');

$include = true;

foreach($arquivos as $file_js_css) {
	$URI = $basefolder.$file_js_css;
	include $basefolder.'ecompress/css_and_javascript_optimized.php';
	//echo $URI;
}