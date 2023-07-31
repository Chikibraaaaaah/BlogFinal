<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class CommentController extends MainController
{

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
}