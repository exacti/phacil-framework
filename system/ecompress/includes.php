<?php

if (!file_exists("/var/zpanel/hostdata/zadmin/exacti_com_br/menu.php")) {
$basefolder = "/Library/WebServer/Documents/exacti5/";
$baseurl = "http://localhost:131/exacti5/";
$prev_u = "exacti5/"; 
} else { 
$basefolder = "/var/zpanel/hostdata/zadmin/exacti_com_br/";
$baseurl = "https://www.exacti.com.br/"; 
$prev_u = ""; 
}

//$arquivos = array('/exacti5/bootstrap/css/bootstrap.min.css', '/exacti5/bootstrap/responsive-tables.js');

$include = true;

foreach($arquivos as $file_js_css) {
	$URI = $basefolder.$file_js_css;
	include $basefolder.'ecompress/css_and_javascript_optimized.php';
	//echo $URI;
}