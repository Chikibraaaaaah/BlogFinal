<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HomeController
 * Manages the Homepage
 * @package App\Controller
 */
class HomeController extends MainController
{
    /**
     * Renders the View Home
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        

        if(!isset($_GET["access"])){
            $page = "home";
        }

        switch ($page) 
        {
            case "home":
                echo $this->twig->render("home.twig", ["allPublications" => ModelFactory::getModel("Article")->listData()]);
                return;
                break;

            case "auth":
                echo $this->twig->render("auth/login.twig");
                return;
                break;

            case "account":
                echo $this->twig->render("account.twig", ["user" => ModelFactory::getModel("Utilisateur")->readData()]);
                return;
                break;

            default : 

                echo $this->twig->render("home.twig", ["allPublications" => ModelFactory::getModel("Article")->listData()]);
                return;
            }

            
            


        $allPublications = ModelFactory::getModel("Articles")->listData();

        return $this->twig->render("home.twig", ["allPublications" => $allPublications]);
    }
}