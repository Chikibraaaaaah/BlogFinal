<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;
use App\Model\Utilisateur;
use App\Model\Factory\PdoFactory;

class AuthController extends MainController
{
    // public function __construct(){
    //     var_dump($this->getPost);
    // }

    public function defaultMethod(){
        echo $this->twig->render("auth/login.twig");
        return;
    }

    public function loginMethod(){


        // echo "<pre>"; 
        // var_dump($this->getRequest("email"));

        

        // echo "</pre>";
        // var_dump($this->getPost);

        $exist = ModelFactory::getModel("Utilisateur")->listData($this->getRequest("email"),"email");
        // var_dump($exist);

        if(!count($exist) == 1){
        
            // $this->$_SESSION["alert"] = [
            //     "type" => "danger",
            //     "message" => "Utilisateur introuvable"
            // ];

            // echo $this->twig->render("auth/login.twig");
            echo "Utilisateur introuvable";
            return;
        }
        $password = $this->getPost("password");

        // echo "<pre>"; 
        // var_dump($this->getPost("password"));

        // echo "</pre>";

        $password = ModelFactory::getModel("Utilisateur")->listData($this->getRequest("password"),"password");

        if(!count($password) == 1){

            echo "Mot de passe incorrect";
            echo $this->twig->render("auth/login.twig");
            return;
        
            // $this->$_SESSION["alert"] = [
            //     "type" => "danger",
            //     "message" => "Mot de passe incorrect"
            // ];
        }

        $_SESSION["user"] = $exist[0];

    return $this->twig->render("home.twig");

    }
}