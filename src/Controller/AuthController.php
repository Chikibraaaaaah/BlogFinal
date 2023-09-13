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

    /**
     * A description of the entire PHP function.
     *
     * @return Some_Return_Value
     */
    public  function defaultMethod()
    {

        $message = $this->getSession() ??"";

        return $this->twig->render("auth/auth.twig", [
            "alert"     => "danger",
            "message"   => $message,
            "method"    => "login"
        ]);

    }


    /**
     * Create an account using the createAccountMethod.
     *
     * @return string The rendered template for the authentication page.
     */
    public  function createAccountMethod()
    {

        $message = $this->getSession()["alert"]["message"] ?? "" ;

        return $this->twig->render("auth/auth.twig", [
            "alert"     => "danger",
            "message"   => $message,
            "method"    => "signup"
        ]);

    }


    /**
     * Register a method.
     *
     * @return string The rendered template.
     */
    public  function registerMethod()
    {

        $message = $this->getSession()["alert"]["message"] ?? "" ;

        return $this->twig->render("auth/auth.twig", [
            "alert"     => "danger",
            "message"   => $message,
            "method"    => "login"
        ]);

    }


    /**
     * Validates the user input, creates a new user account, and redirects to the home page.
     *
     * @return void
     */
    public  function signupMethod()
    {

        if ($this->checkInputs() === TRUE) {
            $existingUser = $this->checkByEmail();  
            if ($existingUser === NULL) {
                $mpChek = $this->checkPasswordsCorrespond();
                if ($mpChek === TRUE) {
                    $hashedPassword = password_hash($this->getPost("password"), PASSWORD_DEFAULT);
                    $newUser = [
                       "userName"   => $this->getPost("userName"),
                       "email"      => $this->getPost("email"),
                       "password"   => $hashedPassword,
                       "createdAt"  => date("Y-m-d H:i:s")
                    ];
                    ModelFactory::getModel("User")->createData($newUser);
                    $userCreated = ModelFactory::getModel("User")->readData($newUser["email"], "email");

                    $this->setSession($userCreated, true);
                    $userCreated["isLogged"] = true;

                    $home = $this->redirect("home");
                    header("Location: ".$home);
                }

                $this->setSession([
                    "alert"   => "danger",
                    "message" => "Les mots de passe ne correspondent pas."
                ]);

                return $this->createAccountMethod();
            }
        }

        $this->setSession([
            "alert"   => "danger",
            "message" => "Veuillez remplir tous les champs."
        ]);

    }


    /**
     * Login method.
     * checks the user input, retrieves the user data from the database,
     * and verifies the password. If the input is valid and 
     * the password matches, it sets the user session and redirects
     * to the home page. Otherwise, it sets an error message and redirects 
     *to the registration page. If the input is invalid or incomplete, it sets an
     *error message and redirects back to the login page.
     * @return void
     */
    public  function loginMethod()
    {

        if ($this->checkInputs()) {

            $user = ModelFactory::getModel("User")->listData($this->getPost("email"),"email")[0];

            if (!$user) {
                $this->setSession([
                    "alert"   => "danger",
                    "message" => "Email non reconnu."
                ]);
                $this->redirect("auth_register");
            }

            if (password_verify($this->getPost("password"), $user['password'])) {
                $user["isLogged"] = true;
                $this->setSession($user, true);
                $this->setSession([
                    "alert"   => "success",
                    "message" => "Connexion réussie."
                ]);
                $home = $this->redirect("home");
                header("Location: $home");
            }

            $this->setSession([
                "alert"   => "danger",
                "message" => "Mot de passe invalide."
            ]);
            $this->redirect("auth_register");
        }

        $this->setSession([
            "alert"   => "danger",
            "message" => "Veuillez remplir tous les champs."
        ]);

        return $this->redirect("auth");

    }


    /**
     * Check user by email.
     *
     * @throws Some_Exception_Class description of exception
     * @return mixed
     */
    private  function checkByEmail()
    {

        $email = $this->getPost("email");
        $userFound = ModelFactory::getModel("User")->listData($email,"email");

        if ($userFound) {
            return $userFound[0];
        }

    }


    /**
     * Checks the user by their username.
     *
     * @throws Some_Exception_Class A description of the exception that can be thrown.
     * @return Some_Return_Value The value returned by the function.
     */
    private  function checkByUserName()
    {

        $userName = $this->getPost("userName");
        $userFound = ModelFactory::getModel("User")->listData($userName,"userName");

        if ($userFound){
            return $userFound[0];
        }

    }


    /**
     * Checks if the passwords entered by the user correspond to each other.
     *
     * @return bool Returns true if the passwords correspond, false otherwise.
     */
    private  function checkPasswordsCorrespond()
    {

        $password = $this->getPost("password");
        $secondPassword = $this->getPost("password_check");

        if ($password != $secondPassword){
            return false;
        }
        return true;

    }


    /**
     * Logout the user and redirect to the home page.
     *
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public  function logoutMethod()
    {

        $home = $this->redirect("home");
        $this->destroyGlobal();
        header("Location: ".$home);

    }

}
