<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;
use App\Model\Utilisateur;
use App\Model\Factory\PdoFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AuthController extends MainController
{

    private $userName;
    private $email;
    private $password;

    public function defaultMethod(){

        $message = $this->getSession()["alert"]["message"] ?? "" ;
        
        return $this->twig->render("auth/login.twig", ["alert" => "danger", "message" => $message]);
    }

    public function createAccountMethod(){
        return $this->twig->render("auth/signup.twig");
    }


    public function signupMethod(){

        $existingUser = ModelFactory::getModel("Utilisateur")->listData($this->email, "email");

        if($existingUser){
            $this->setSession(["alert" => "danger", "message" => "Cette adresse email est déjà utilisée"]);
            return  $this->redirect("auth_createAccount"); 
        }

        if($this->getPost("password") != $this->getPost("passwordCheck")){
            $this->setSession(["alert" => "danger", "message" => "Les mots de passe ne correspondent pas"]);
            return $this->redirect("auth_createAccount"); 
        }

        $hashedPassword = password_hash($this->getPost("password"), PASSWORD_DEFAULT);

        $newUser = [
            "userName"=> $this->getPost("userName"),
            "email" => $this->getPost("email"),
            "password" => $hashedPassword,
            "creationDate" =>  date("Y-m-d H:i:s")
        ];

        ModelFactory::getModel("Utilisateur")->createData($newUser);

        $userCreated = ModelFactory::getModel("Utilisateur")->readData($newUser["email"], "email");

        $this->setSession($userCreated, true);
        
        return $this->redirect("home");
    }

    public function loginMethod()
    {
        if($this->checkInputs()){

            $user = ModelFactory::getModel("Utilisateur")->listData($this->getPost("email"), "email")[0];

            if(!$user){
                $this->setSession(["alert" => "danger", "message" => "Email non reconnu"]);
                return $this->twig->render("auth/login.twig", ["alert" => "danger", "message" => $_SESSION["alert"]["message"]]);
            }

            if (password_verify($this->getPost("password"), $user['password'])) {
                $this->setSession($user, true);
                return $this->redirect("home");
            }

            $this->setSession(["alert" => "danger", "message" => "Mot de passe invalide"]);
            
            return $this->redirect("auth_login");
        }

        return $this->redirect("auth");
        
    }

     
    
    public function logoutMethod(){
        $this->destroyGlobal();
        return $this->redirect("home");
    }
}