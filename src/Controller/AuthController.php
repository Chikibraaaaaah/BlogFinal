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
        
        return $this->twig->render("auth/auth.twig", ["alert" => "danger", "message" => $message, "method" => "login"]);
    }

    public function createAccountMethod(){

        $message = $this->getSession()["alert"]["message"] ?? "" ;

        return $this->twig->render("auth/auth.twig", ["alert" => "danger", "message" => $message, "method" => "signup"]);
        
    }


    public function signupMethod(){

        if( $this->checkInputs()){
            $existingUser = ModelFactory::getModel("User")->listData($this->email, "email");

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
                "createdAt" =>  date("Y-m-d H:i:s")
            ];

            ModelFactory::getModel("User")->createData($newUser);

            $userCreated = ModelFactory::getModel("User")->readData($newUser["email"], "email");

            $this->setSession($userCreated, true);
            $user["isLogged"] = true;
            
            return $this->redirect("home");
        }

        $this->setSession(["alert" => "danger", "message" => "Veuillez remplir tous les champs."]);


    }

    public function loginMethod()
    {
        if($this->checkInputs()){

            $user = ModelFactory::getModel("User")->listData($this->getPost("email"), "email")[0];

            if(!$user){
                $this->setSession(["alert" => "danger", "message" => "Email non reconnu."]);
                return $this->twig->render("auth/login.twig", ["alert" => "danger", "message" => $this->getSession()["alert"]["message"]]);
            }

            if (password_verify($this->getPost("password"), $user['password'])) {
                $user["isLogged"] = true;
                $this->setSession($user, true);
                $this->setSession(["alert" => "success", "message" => "Connexion réussie."]);
                return $this->redirect("home");
            }

            $this->setSession(["alert" => "danger", "message" => "Mot de passe invalide."]);
            
            return $this->redirect("auth_login");
        }

        $this->setSession(["alert" => "danger", "message" => "Veuillez remplir tous les champs."]);

        return $this->redirect("auth");
        
    }

    private function checkByEmail(){

        $email = $this->getPost("email");

        $userFound = ModelFactory::getModel("User")->listData($email, "email");

        if($userFound){

            return $userFound[0];

        }
    }

     
    
    public function logoutMethod(){
        $this->destroyGlobal();
        $user["isLogged"] = false;
        return $this->redirect("home");
    }
}