<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class CommentController extends MainController
{

    public function defaultMethod(){
        $articleId = $this->getComment()["articleId"];
        $article = ModelFactory::getModel("Article")->readData($articleId, "id");
        $comments = ModelFactory::getModel("Commentaire")->readData(intval($articleId), "articleId");

        return $this->twig->render("articles/simpleArticle.twig", ["article" => $article, "comments" => $comments]);
    }

    public function createCommentMethod(){

        $auteur = $this->getSession()["user"]["id"];
        $commentaire = $this->getPost("comment");
        $articleId = $this->getSession()["alert"]["article"];

        $newComment = [
            "auteurId" => $auteur,
            "articleId" => intval($articleId),
            "contenu" => $commentaire,
            "datePublication" => date("Y-m-d H:i:s")
        ];

        ModelFactory::getModel("Commentaire")->createData($newComment);

        $this->setSession(["alert" => "success", "message" => "Nous nous réservons le droit à une première lecture avant de publier votre commentaire. Merci pour votre compréhension"]);

        $this->redirect("article_getArticleById", ["id" => $articleId]);
    }

    public function updateMethod(){
       
        $articleId = $this->getComment("articleId")["articleId"];
        $comments = ModelFactory::getModel("Commentaire")->readData(intval($articleId), "articleId");
        $article = ModelFactory::getModel("Article")->readData($articleId, "id");

        var_dump($this->checkUser());

        return $this->twig->render("articles/simpleArticle.twig", ["article" => $article, "comment" => $comments, "method" => "update"]);
    } 

    public function getComment(){
       
        $id = $this->getGet("commentId");
        $comment = ModelFactory::getModel("Commentaire")->readData($id, "id");

        return $comment;

    }


}