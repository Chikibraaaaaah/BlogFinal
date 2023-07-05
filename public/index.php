<?php 

use Tracy\Debugger;

require_once "../vendor/autoload.php";

Debugger::enable();

$router = new \App\Router();

$router->run();