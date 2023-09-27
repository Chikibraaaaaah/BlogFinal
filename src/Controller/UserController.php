<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;

class UserController extends MainController 
{

    public function defaultMethod()
    {
        $user = $this->getUserById();
        $comments = ModelFactory::getModel("Comment")->listData($user["id"], "authorId");
        $articlesCommented = [];

        foreach ($comments as $comment) {
            $articles = ModelFactory::getModel("Article")->readData($comment["articleId"], "id");
            $articlesCommented[] = $articles;
        }

        return $this->twig->render("users/userAccount.twig", [
            "user" => $user,
            "articlesCommented" => $articlesCommented,
            "comments" => $comments
        ]);
    }

    public function getUserById()
    {
        $id = $this->getGet("id");
        $user = ModelFactory::getModel("User")->readData($id, "id");

        if ($user["imgUrl"] === NULL) {
            $user["imgUrl"] = "../public/img/7325a06d4db2bd83295171a1328d0be8e11b77d5.jpeg";
        }

        return $user;
    }

    public function updateUserMethod()
    {
        $user = $this->getUserById();

        
    }

    // public function getUserPost(){

    //     $user = ModelFactory::getModel("User")->readData($this->getGet("id"), "id");
    //     $comments = ModelFactory::getModel("Comment")->listData($user["id"], "authorId");

    //     var_dump($comments);
    //     die();
    // }

    public function updatePictureMethod()
    {

        $user = $this->getUserById();
        $newPic = $this->getFiles("imgUrl");


    }





}