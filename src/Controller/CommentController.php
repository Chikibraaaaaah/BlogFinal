<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;

class CommentController extends MainController
{

    protected $auteurId;

    protected $articleId;

    protected $content;


    public  function defaultMethod()
    {

    }


    /**
     * Creates a new comment for an article.
     *
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public  function createCommentMethod()
{

    $this->auteurId = $this->getSession()["user"]["id"];
    $this->content = $this->getPost("content");
    $this->articleId = $this->getGet("id");

    $newComment = [
        "authorId"   => (int)$this->auteurId,
        "articleId"  => (int)$this->articleId,
        "content"    => $this->content,
        "createdAt"  => date("Y-m-d H:i:s")
    ];

    ModelFactory::getModel("Comment")->createData($newComment);

    $this->setSession([
        "alert"     => "success",
        "message"   => "Nous nous réservons le droit à une première lecture avant de publier votre commentaire. Merci pour votre compréhension"
    ]);

    $articleId = urlencode($this->articleId);
    $article = $this->redirect("article_renderArticle", ["id" => (int) $articleId]);
    header("Location: " . $article);

}


    /**
     * Updates a comment method.
     *
     * @throws Some_Exception_Class description of exception
     * @return void
     */
    public function updateCommentMethod()
    {

        $existingComment = ModelFactory::getModel("Comment")->listData($this->getCommentById(),"id")[0];

        if($this->checkInputs()) {

            $updatedComment = array_merge($existingComment, $this->getPost()["content"]);
            $updatedComment["content"] = PDO::quote($updatedComment["content"]);
            $updatedComment["updatedAt"] = date("Y-m-d H:i:s");

            ModelFactory::getModel("Comment")->updateData($existingComment["id"], $updatedComment);

            $this->redirect("article_renderArticle", [
                "id"=> $updatedComment["articleId"]
            ]);

        }

    }


    /**
     * Retrieves a comment by its ID.
     *
     * @throws Some_Exception_Class if the comment ID is not provided
     * @return int The ID of the comment
     */
    public function getCommentById()
    {

        $commentId = $this->getGet()["id"];

        return $commentId;

    }


    /**
     * Edit the comment method.
     *
     * @return string The rendered template.
     */
    public function editCommentMethod()
    {
        $commentaire = ModelFactory::getModel("Comment")->listData($this->getGet("id"), "id")[0];
        $article = ModelFactory::getModel("Article")->readData($commentaire["articleId"], "id");
        $relatedComments = ModelFactory::getModel("Comment")->listData($article["id"], "articleId");
    
        return $this->twig->render("articles/article.twig", [
            "article"           => $article,
            "myCommentaire"     => $commentaire,
            "relatedComments"   => $relatedComments,
            "user"              => $this->getSession()["user"],
            "method"            => "PUT"
        ]);
    }


    /**
     * Confirm the deletion of a comment.
     *
     * @throws Some_Exception_Class Description of exception.
     * @return Some_Return_Value
     */
    public function confirmDeleteCommentMethod()
    {
    
        $this->setSession([
            "alert"            => "danger",
            "message"          => "Êtes-vous certain de vouloir supprimer ce commentaire ?"
        ]);
    
        return $this->twig->render("alert.twig", [
            "alert"                => "danger",
            "message"              => $this->getSession()["alert"]["message"],
            "commentaire"          => ModelFactory::getModel("Commentaire")->readData($this->getGet("id"))
        ]);
    
    }


    /**
     * Deletes a comment.
     *
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function deleteCommentMethod()
    {
        $id = $this->getRequest()["id"];
        $articleId = ModelFactory::getModel("Comment")->listData()[0]["articleId"];
    
        ModelFactory::getModel("Comment")->deleteData($id);
    
        return $this->redirect("article_getArticle", [
            "id" => (int)$articleId
        ]);
    }


}
