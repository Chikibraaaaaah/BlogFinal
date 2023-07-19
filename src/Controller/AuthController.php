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

        return $this->twig->render("auth/login.twig");
        
    }

    function loginMethod()
    {
        if(empty($this->getPost("email")) || empty($this->getPost("password"))){
            $this->setSession(["alert" => "danger", "message" => "Veuillez remplir tous les champs"]);
            return $this->twig->render("auth/login.twig", ["alert" => "danger", "message" => $_SESSION["alert"]["message"]]);
        }

        // Vérifier si l'utilisateur existe dans la base de données
        $this->email = $this->getPost("email");
        $this->password = $this->getPost("password");

        $user = ModelFactory::getModel("Utilisateur")->listData($this->email, "email")[0];

        // var_dump($user);
        // die();

        if(!$user){
            $this->setSession(["alert" => "danger", "message" => "Email non reconnu"]);
            return $this->twig->render("auth/login.twig", ["alert" => "danger", "message" => $_SESSION["alert"]["message"]]);
        }

        if ($user) {

            // var_dump($user);
            // die();
            // Vérifier si le mot de passe correspond
            if (password_verify($this->password, $user['password'])) {
                // Connecter l'utilisateur en créant une session
                $this->setSession($user, true);
                return $this->redirect("home");
            }
        }
       
        $this->setSession(["alert" => "danger", "message" => "Mot de passe invalide"]);
        return $this->twig->render("auth/login.twig", ["alert" => "danger", "message" => $_SESSION["alert"]["message"]]);

    }

    public function signupMethod(){
        $this->userName = $this->getPost("userName");
        $this->email = $this->getPost("email");
        $this->password = $this->getPost("password");

        $existingUser = ModelFactory::getModel("Utilisateur")->listData($this->email, "email");

        if($existingUser){
            $this->setSession(["alert" => "danger", "message" => "Cette adresse email est déjà utilisée"]);
            return $this->twig->render("auth/signup.twig", ["alert" => "danger", "message" => $_SESSION["alert"]["message"]]); 
        }

        if($this->getPost("password") != $this->getPost("passwordCheck")){
            $this->setSession(["alert" => "danger", "message" => "Les mots de passe ne correspondent pas"]);
            return $this->twig->render("auth/signup.twig", ["alert" => "danger", "message" => $_SESSION["alert"]["message"]]); 
        }

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $newUser = [
            "userName"=> $this->userName,
            "email" => $this->email,
            "password" => $hashedPassword,
            "creationDate" =>  date("Y-m-d H:i:s")
        ];

        ModelFactory::getModel("Utilisateur")->createData($newUser);
        unset($newUser["password"]);
        $this->setSession(["user" => $newUser], true);
        $this->redirect("home");
        return $this->twig->render("home.twig", ["logged"=>$_SESSION["user"]]);
    }

    public function createAccountMethod(){
        return $this->twig->render("auth/signup.twig");
    }
            
    
    public function logoutMethod(){
        $this->destroyGlobal();
        return $this->redirect("home");
    }
}