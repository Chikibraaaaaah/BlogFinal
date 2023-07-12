<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class ArticleController extends MainController
{

   public function defaultMethod(){
      // if(($_SESSION["role"] === 0)){
      //   return  false;
      // }

      // return true;
      //   echo "<pre>";
      // var_dump($_SESSION);
      // echo "</pre>";

      // return $this->twig->render("articles/gallerie.twig", ["articles" => ModelFactory::getModel("Publication")->listData()]);

      // $id = $this->getGet("id");
      // $publi = $this->twig->render("articles/simpleArticle.twig", ["articles" => ModelFactory::getModel("Article")->listData($id)]);
      // var_dump($publi);

      var_dump($this->method);
   }

    // public function createPublicationMethod(){

    // }

    public function getArticleMethod(){

      $id = $this->getGet("id");

      $article =   ModelFactory::getModel("Article")->listData(intval($id), "id");
      $comments = ModelFactory::getModel("Commentaire")->listData(intval($id), "publicationId");
      $nbComments = count($comments);

      $_SESSION["publiToComment"] = $id;
      $this->setSession(["publiToComment" => $id]);
      var_dump($_SESSION);


      return $this->twig->render("articles/simpleArticle.twig", ["article" => $article, "comments" => $comments]);
    }



    
}