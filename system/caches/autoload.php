<?php
/**
 * Copyright (c) 2019. ExacTI Technology Solutions
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

if(defined('USE_PHPFASTCACHE') && USE_PHPFASTCACHE == true && version_compare(phpversion(), '7.0.0', '>'))
    require_once DIR_SYSTEM."caches/phpfastcache.php";
else
    require_once DIR_SYSTEM."caches/caches.php";