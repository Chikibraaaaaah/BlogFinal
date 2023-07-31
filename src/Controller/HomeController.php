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
        $articles = $this->getArticles();
        $user = $this->getSession("user");
        $alerts = $this->getAlert("alert");

        return  $this->twig->render("home.twig", [
                    "allPublications" => $articles,
                    "errors" => $alerts,
                    "logged" => $user
                ]);
    }

    public function getArticles(){
    
        $articles = ModelFactory::getModel("Article")->listData();

        return $articles;
   }


}