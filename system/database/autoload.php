<?php

define('DIR_DATABASE', (__DIR__)."/database/");

include __DIR__."/library/db.php";

if(defined('DB_DRIVER')) {
    global $db;
    $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
} else {
    global $db;
    $db = new DB('nullStatement', NULL, NULL, NULL, NULL);
}
