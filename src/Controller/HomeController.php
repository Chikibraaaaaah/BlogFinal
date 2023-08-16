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

        $comments = [];

        foreach ($articles as $article => $value) {
            
            $id = $value["id"];

            $relatedComments = ModelFactory::getModel("Commentaire")->listData($id, "articleId");

            $comments[] = $relatedComments;
        }

        return  $this->twig->render("home.twig", [
                    "articles" => $articles,
                    "alert" => $alerts,
                    "user" => $user,
                    "commentaires" => $comments[0]
                ]);
    }

    public function getArticles(){
    
        $articles = ModelFactory::getModel("Article")->listData();

        return $articles;
   }

}