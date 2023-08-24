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
        $this->contenu = $this->getPost("contenu");
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

    public function updateCommentMethod(){
       
        $existingComment = ModelFactory::getModel("Commentaire")->listData($this->getCommentById(), "id")[0];     
    var_dump($this->checkInputs());
    die();
        if ($this->checkInputs()) {

            $updatedComment = array_merge($existingComment, $this->getPost()["contenu"]);
            $updatedComment["contenu"] = addslashes($updatedComment["contenu"]);
            $updatedComment["dateModification"] = date("Y-m-d H:i:s");
    
            ModelFactory::getModel("Commentaire")->updateData($existingComment["id"], $updatedComment);

            var_dump(ModelFactory::getModel("Commentaire")->listData($this->getCommentById(), "id")[0]);
            die();

            // $this->redirect("article_renderArticle", ["id" => $updatedComment["articleId"]]);

        } 
}

    public function getCommentById(){
       
        $commentId = $this->getGet()["id"];

        return $commentId;

    }

    public function editCommentMethod(){

        $commentaire = ModelFactory::getModel("Commentaire")->listData($this->getGet("id"), "id")[0];
        $article = ModelFactory::getModel("Article")->readData($commentaire["articleId"], "id");
        $relatedComments = ModelFactory::getModel("Commentaire")->listData($article["id"], "articleId");

        return $this->twig->render("articles/simpleArticle.twig", [ "article" => $article, "myCommentaire" => $commentaire, "relatedComments" => $relatedComments, "user" => $this->getSession()["user"], "method" => "PUT" ]);

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