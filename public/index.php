<?php 

use Tracy\Debugger;

require_once "../vendor/autoload.php";
require_once "../config/config.php";

// session_start();
// echo "<pre>"; 
// var_dump($_SESSION);
// echo "</pre>";

Debugger::enable();

$router = new \App\Router();

$router->run();