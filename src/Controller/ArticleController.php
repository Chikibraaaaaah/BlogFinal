<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class ArticleController extends MainController
{

   public function defaultMethod(){

   }

    // public function createPublicationMethod(){

    // }

    public function getArticleMethod(){

      $article =   ModelFactory::getModel("Article")->listData(intval($this->getGet("id")), "id");
      $comments = ModelFactory::getModel("Commentaire")->listData(intval($this->getGet("id")), "publicationId");
      $nbComments = count($comments);
      $_SESSION["lastArticle"] = $this->getGet("id");

      if($this->getSession("user")){
        // var_dump($this->getSession("user"));
        // die();
        return $this->twig->render("articles/simpleArticle.twig", ["article" => $article, "comments" => $comments, "logged" => $this->getSession("user"), "nbComments" => $nbComments]);
      }

      return $this->twig->render("articles/simpleArticle.twig", ["article" => $article, "comments" => $comments]);
    }



    
}