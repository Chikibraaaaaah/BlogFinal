<?php 

use Tracy\Debugger;

require_once "../vendor/autoload.php";

session_start();

Debugger::enable();

$router = new \App\Router();

$router->run();