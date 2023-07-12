<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;
use App\Model\Utilisateur;
use App\Model\Factory\PdoFactory;

class AuthController extends MainController
{


    public function defaultMethod(){
        var_dump($_SESSION);
        return $this->twig->render("auth/login.twig");
        
    }

    public function loginMethod()
    {

        $_SESSION["publiToComment"] = $_SESSION["publiToComment"] ?? null;


        $exist = ModelFactory::getModel("Utilisateur")->listData($this->getRequest("email"), "email");
    
        if (!count($exist) == 1) {
            $this->setSession(["alert" => "danger", "message" => "Utilisateur introuvable"]);
            $session = $_SESSION;

            var_dump($_SESSION);
            $alert = $session["alert"];
            $message = isset($alert["message"]) ? $alert["message"] : '';
            return $this->twig->render("auth/login.twig", ["alert" => $alert, "message" => $message]);
        }
    
        $password = $this->getPost("password");
    
        $password = ModelFactory::getModel("Utilisateur")->listData($this->getRequest("password"), "password");
    
        if (!count($password) == 1) {
            // $this->setSession(["type" => "danger", "message" => "Mot de passe incorrect"]);
            // $session = $this->getSession();
            $alert = $session["alert"];
            $message = isset($alert["message"]) ? $alert["message"] : '';
            return $this->twig->render("auth/login.twig", ["alert" => $alert, "message" => $message]);
        }
    
        $_SESSION["user"] = $exist[0];
        // var_dump($_SESSION["publiToComment"]);
        // return $this->twig->render("home.twig", ["user" => $_SESSION["user"]]);
    }
}