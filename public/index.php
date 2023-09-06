<?php 

use Tracy\Debugger;

require_once "../vendor/autoload.php";

Debugger::enable();

$router = new \App\Router();

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$router->run();

