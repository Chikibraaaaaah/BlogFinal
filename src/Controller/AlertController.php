<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;


class AlertController extends MainController 
{

    private $type;
    private $message;


    public function getMessageAlert(){

        $this->message = $this->getAlert();
        var_dump($this->message);
        die();

    }

}