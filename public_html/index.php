<?php

/**
 * Copyright (c) 2019. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

$baseDir = str_replace('public_html', '', __DIR__); // change this with config.php directory.


// start Phacil engine
require_once($baseDir."config.php");

$tipe = (isset($_GET['_type_']))? $_GET['_type_'] : '';

switch($tipe) {
	case 'img':
		require_once(DIR_SYSTEM.'ecompress/optimeze_img.php');
		break;
	case 'script':
		require_once(DIR_SYSTEM.'ecompress/css_and_javascript_optimized.php');
		break;
	default:
		require_once(DIR_SYSTEM.'system.php');
		break;
}


