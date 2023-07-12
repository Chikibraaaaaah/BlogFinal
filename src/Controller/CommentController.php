<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;

class CommentController extends MainController
{
    private $autor;

    private $publicationId;

    private $content;

    private $validate;

    private $datePublication;

    private $dateModification; 

    public function createCommentMethod(){

  
        var_dump($_SESSION["publiToComment"]);

    }
}