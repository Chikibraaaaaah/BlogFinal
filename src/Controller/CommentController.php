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


        // var_dump($this->getSession("user")["id"]);
        $newComment = [
            $this->autorId = intval($this->getSession("user")["id"]),
            $this->publicationId = intval($_SESSION["lastArticle"]),
            $this->content = $this->getPost("comment"),
            $this->datePublication = date("Y-m-d H:i:s")
        ];

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