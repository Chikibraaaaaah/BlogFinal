<?php 

use Tracy\Debugger;

require_once "../vendor/autoload.php";
// require_once "../config/config.php";

Debugger::enable();

$router = new \App\Router();

if(!isset($_SESSION)){
    session_start();
}

$router->run();

