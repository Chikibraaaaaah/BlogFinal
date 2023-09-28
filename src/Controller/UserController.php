<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;

class UserController extends MainController 
{

public function defaultMethod()
{
    
}

    public function getUserMethod(){

        $user = $this->getUserById();
        $comments = ModelFactory::getModel("Comment")->listData($user["id"], "authorId");
        $articleIdsCommented = [];

        foreach ($comments as $comment) {
            $articleId = $comment["articleId"];

            if (!in_array($articleId, $articleIdsCommented)) {
                $articleIdsCommented[] = $articleId;
            }
        }

        $numArticlesCommented = count($articleIdsCommented);

        return $this->twig->render("users/userAccount.twig", [
            "user" => $user,
            "numArticlesCommented" => $numArticlesCommented,
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

    public function getUser(){

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


    public function updateUserInfosMethod()
    {

        $user = $this->getSession("user");
        $destination = $user["imgUrl"];

        if ($this->getFiles()["img"]["size"] > 0 && $this->getFiles()["img"]["size"] < 1000000) {
            $destination = $this->updateFile();
        }

        if ($this->checkInputs() === TRUE) {
            $updatedUser = array_merge($user, $this->getPost());
            $updatedUser["imgUrl"] = $destination;

            ModelFactory::getModel("User")->updateData((int) $updatedUser["id"], $updatedUser);

            return $this->redirect("user_getUser", ["id" => (int) $updatedUser["id"]]);
        }
    }


    public function updatePictureMethod()
    {

        $destination = $this->uploadFile();
        $user = $this->getSession("user");
        $user["imgUrl"] = $destination;
    
        ModelFactory::getModel("User")->updateData($user["id"], $user);
    
     return $this->setSession(["alert" => "success", "message" => "Votre photo de profil a bien été mise à jour"]);
       
    }

    public function deleteFile()
    {
        $imgPath = $this->getUserById()["imgUrl"];

        if (file_exists($imgPath) === TRUE) {
            unlink($imgPath);
            return ;
        }

        return $this->setSession([
            "alert" => "danger",
            "message" => "Le fichier n'existe pas"
        ]);
    }

    public function checkFileError()
    {
        switch ($this->getFiles()['img']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('Aucun fichier transmis.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Taille maximale atteinte. Max : 1MB.');
        default:
            throw new RuntimeException('Erreur non identifiée.');
        }
    }


    /**
     * Check the MIME Type of a file.
     * This function checks the MIME Type of a file by using the `mime_content_type` function.
     * It retrieves the file MIME Type from the uploaded image file and compares it with a list of valid MIME Types.
     * If the MIME Type is not found in the list of valid types, it sets a session variable with an error message.
     * @return string|void Returns the file extension if it is a valid MIME Type, or void if it is not.
     */
    public function checkFileMime()
    {
        // Check MIME Type by yourself!
        $fileMimeType = mime_content_type($this->getFiles()['img']['tmp_name']);
        $validMimeTypes = [
            "jpg"   => "image/jpg",
            "jpeg"  => "image/jpeg",
            "png"   => "image/png",
            "gif"   => "image/gif"
        ];

        $ext = array_search($fileMimeType, $validMimeTypes, true);

        if ($ext === false) {
            return $this->setSession(["alert" => "danger", "message" => "Format invalide."]);
        // Throw new RuntimeException('Invalid file format.')!
        }

        return $ext;
    }

    public function updateFile()
    {
        if ($this->getFiles()["img"]["size"] > 0 && $this->getFiles()["img"]["size"] < 1000000) {
            $this->deleteFile();
            $destination = $this->uploadFile();

            return $destination;
        }

    }



}