<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class ArticleController extends MainController
{

  private $titre;
  private $contenu;
  private $img;
  private $datePublication;

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

    public function createArticleMethod(){

      $this->titre = $this->getPost("titre");
      $this->contenu = $this->getPost("contenu");
      $this->img = $this->getFiles();
      $this->datePublication = date("Y-m-d");
      // var_dump($this->titre);
      // var_dump($_POST);
      // echo "<pre>"; 
      // var_dump($_REQUEST);
      // echo "</pre>"; 

      echo "<pre>"; 
      var_dump($_FILES);
      echo "</pre>"; 
      die();
    }



    
}