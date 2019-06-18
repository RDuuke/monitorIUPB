<?php

include_once "defines.php";
include_once BASE_DIR . "vendor" . DS . "autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::create(BASE_DIR);
$dotenv->load();
$app = new \Slim\App(require_once "settings.php");

require_once "containers.php";
require_once SRC . "routes.php";
$app->run();
