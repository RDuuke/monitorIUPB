<?php
header('Content-Type: text/html; charset=ISO-8859-1');
session_start();
date_default_timezone_set("America/Bogota");
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 1800); //3 minutes
error_reporting(E_ALL);
define("DS", DIRECTORY_SEPARATOR);
define("BASE_DIR",  dirname(__DIR__) . DS);
define("SRC",  BASE_DIR . "src" . DS);
define("RESOURCE",  BASE_DIR . "resource" . DS);
