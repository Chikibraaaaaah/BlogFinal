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

        return $this->twig->render("users/userAccount.twig", [
            "user" => $user
        ]);
    }

    private function getUserById()
    {
        $id = $this->getGet("id");
        $user = ModelFactory::getModel("User")->readData($id, "id");

        if ($user["imgUrl"] === NULL) {
            $user["imgUrl"] = "../public/img/7325a06d4db2bd83295171a1328d0be8e11b77d5.jpeg";
        }

        return $user;
    }





}