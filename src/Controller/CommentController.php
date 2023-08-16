<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class CommentController extends MainController
{

    protected $auteurId;

    protected $articleId;

    protected $contenu;


    public function defaultMethod(){

    }

    public function createCommentMethod(){

        $this->auteurId = $this->getSession()["user"]["id"];
        $this->contenu = $this->getPost("commentaire_contenu");
        $this->articleId = $this->getGet("id");

        $newComment = [
            "auteurId" => intval($this->auteurId),
            "articleId" => intval($this->articleId),
            "contenu" => $this->contenu,
            "datePublication" => date("Y-m-d H:i:s")
        ];

        ModelFactory::getModel("Commentaire")->createData($newComment);

        $this->setSession([
            "alert" => "success",
            "message" => "Nous nous réservons le droit à une première lecture avant de publier votre commentaire. Merci pour votre compréhension"
         ]);

        return $this->redirect("article_renderArticle", ["id" => intval($this->articleId)]);
    }

    // public function updateMethod(){
       
    //     $articleId = $this->getComment("articleId")["articleId"];
    //     $comments = ModelFactory::getModel("Commentaire")->readData(intval($articleId), "articleId");
    //     $article = ModelFactory::getModel("Article")->readData($articleId, "id");

    //     var_dump($comments);

    //     return $this->twig->render("articles/simpleArticle.twig");
    // } 

    public function getComment(){
       
        $id = $this->getGet("commentId");
        $comment = ModelFactory::getModel("Commentaire")->readData($id, "id");

        return $comment;

    }

    public function confirmDeleteCommentMethod(){
        $this->setSession(["alert" => "danger", "message" => "Êtes-vous certain de vouloir supprimer ce commentaire ?"]);

        return $this->twig->render("alert.twig", ["alert" => "danger", "message" => $this->getSession()["alert"]["message"], "commentaire" => ModelFactory::getModel("Commentaire")->readData($this->getGet("id"))]);
    }

    public function deleteCommentMethod(){
        
        $id = $this->getRequest()["id"];
        $articleId = ModelFactory::getModel("Commentaire")->listData()[0]["articleId"];

        ModelFactory::getModel("Commentaire")->deleteData($id);

            return $this->redirect("article_getArticle", ["id" => intval($articleId)]);

    }

    


}