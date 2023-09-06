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


    public function defaultMethod()
    {

        $message = $this->getSession() ?? "" ;
        
        return $this->twig->render("auth/auth.twig", ["alert" => "danger", "message" => $message, "method" => "login"]);
    }

    public function createAccountMethod()
    {

        $message = $this->getSession()["alert"]["message"] ?? "" ;

        return $this->twig->render("auth/auth.twig", ["alert" => "danger", "message" => $message, "method" => "signup"]);
        
    }

    public function registerMethod()
    {

        $message = $this->getSession()["alert"]["message"] ?? "" ;

        return $this->twig->render("auth/auth.twig", ["alert" => "danger", "message" => $message, "method" => "login"]);

    }

    public function signupMethod()
    {

        if( $this->checkInputs() === TRUE ){

            $existingUser = $this->checkByEmail();  

            if ( $existingUser == NULL ){

                $mpChek = $this->checkPasswordsCorrespond();

                if( $mpChek == true ) {
                    
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
                    $userCreated["isLogged"] = true;
                    
                    return $this->redirect("home");
                }

                $this->setSession(["alert" => "danger", "message" => "Les mots de passe ne correspondent pas."]);
                
                return $this->createAccountMethod();

            }
        }

        $this->setSession(["alert" => "danger", "message" => "Veuillez remplir tous les champs."]);


    }

    public function loginMethod()
    {
        if($this->checkInputs()){

            $user = ModelFactory::getModel("User")->listData($this->getPost("email"), "email")[0];

            if(!$user){
                $this->setSession(["alert" => "danger", "message" => "Email non reconnu."]);
                $this->redirect("auth_register");
            }

            if (password_verify($this->getPost("password"), $user['password'])) {
                $user["isLogged"] = true;
                $this->setSession($user, true);
                $this->setSession(["alert" => "success", "message" => "Connexion réussie."]);
                $this->redirect("home");
            }

            $this->setSession(["alert" => "danger", "message" => "Mot de passe invalide."]);
            
            $this->redirect("auth_register");
        }

        $this->setSession(["alert" => "danger", "message" => "Veuillez remplir tous les champs."]);

        return $this->redirect("auth");
        
    }

    private function checkByEmail()
    {

        $email = $this->getPost("email");
        $userFound = ModelFactory::getModel("User")->listData($email, "email");

        if($userFound){

            return $userFound[0];

        }
    }

    private function checkByUserName()
    {
        
    }

    private function checkPasswordsCorrespond()
    {
        
        $password = $this->getPost("password");
        $secondPassword = $this->getPost("password_check");

        if($password != $secondPassword){

            return false;

        }

        return true;

    }

     
    
    public function logoutMethod()
    {
        $this->destroyGlobal();
        $user["isLogged"] = false;
        return $this->redirect("home");
    }

   
    
}