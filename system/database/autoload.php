<?php
/*
 * Copyright © 2021 ExacTI Technology Solutions. All rights reserved.
 * GPLv3 General License.
 * https://exacti.com.br
 * Phacil PHP Framework - https://github.com/exacti/phacil-framework
 */

 

if(!defined('DIR_DATABASE'))
    define('DIR_DATABASE', (__DIR__)."/");

include DIR_DATABASE."/library/db.php";

use Phacil\Framework;

if(defined('DB_DRIVER')) {
    global $db;
    $db = new Phacil\Framework\DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
} else {
    global $db;
    $db = new Phacil\Framework\DB('nullStatement', NULL, NULL, NULL, NULL);
}
