<?php 

use Tracy\Debugger;

require __DIR__.'/../vendor/autoload.php';

Debugger::enable();

$router = new \App\Router();

if (empty(session_id())) {
    session_start();
}

$router->run();
