<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class CommentController extends MainController
{
    private $autorId;

    private $publicationId;

    private $content;

    private $validate;

    private $datePublication;

    private $dateModification; 

    public function createCommentMethod(){

        // var_dump(gettype($this->getSession("user")["id"]));



            $this->autorId = (int) $this->getSession("user")["id"];
            $this->publicationId =  (int) $_SESSION["lastArticle"];
            $this->content =  (string) trim($this->getPost("comment")) ;
            $this->datePublication = date("Y-m-d H:i:s");
            $this->dateModification = date("Y-m-d H:i:s") ;


            // var_dump($this->autorId);
            // var_dump($this->publicationId);
            // var_dump($this->content);
            // var_dump($this->datePublication);

            die();

        // var_dump($this->getSession("user")["id"]);
        $newComment = [
            $this->autorId,
            $this->publicationId,
            $this->content,
            $this->datePublication,
            $this->dateModification 
        ];

        // var_dump($newComment);
        // die();

        // echo "auteur";
        // var_dump($this->getPost("comment"));

        // die();

        // echo "publiId";
        // var_dump($this->thi);
        // echo "content";
        // var_dump($this->getPost("comment"));

        ModelFactory::getModel("Commentaire")->createData($newComment);

    }
}