<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class CommentController extends MainController
{

    public function defaultMethod(){

    }

    public function createCommentMethod(){

        $auteur = $this->getSession()["user"]["id"];
        $commentaire = $this->getPost("commentaire_contenu");
        $articleId = $this->getSession()["alert"]["article"];

        $newComment = [
            "auteurId" => $auteur,
            "articleId" => intval($articleId),
            "contenu" => $commentaire,
            "datePublication" => date("Y-m-d H:i:s")
        ];

        ModelFactory::getModel("Commentaire")->createData($newComment);

        $this->setSession([
            "alert" => "success",
            "message" => "Nous nous réservons le droit à une première lecture avant de publier votre commentaire. Merci pour votre compréhension"
         ]);

        return $this->redirect("article_getArticle", ["id" => intval($articleId)]);
    }

    public function updateMethod(){
       
        $articleId = $this->getComment("articleId")["articleId"];
        $comments = ModelFactory::getModel("Commentaire")->readData(intval($articleId), "articleId");
        $article = ModelFactory::getModel("Article")->readData($articleId, "id");

        var_dump($comments);

        return $this->twig->render("articles/simpleArticle.twig");
    } 

    public function getComment(){
       
        $id = $this->getGet("commentId");
        $comment = ModelFactory::getModel("Commentaire")->readData($id, "id");

        return $comment;

    }


}