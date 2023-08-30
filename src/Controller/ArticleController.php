<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;

class ArticleController extends MainController
{
    private $title;
    private $content;
    private $img;
    private $createdAt;

    public function defaultMethod(){
    
    }

    public function getArticleById(){
        
            $articleId = $this->getGet("id");
            $article = ModelFactory::getModel("Article")->readData($articleId,"id");

            return $article;
    }


    public function renderArticleMethod(){

        $article = ModelFactory::getModel("Article")->readData($this->getGet("id"), "id");
        $relatedComments = ModelFactory::getModel("Comment")->listData($article["id"],"articleId");

        return $this->twig->render("articles/articleSingle.twig",[
            "user" => $this->getSession()["user"],
            "article" => $article,
            "comments" => $relatedComments
        ]);

    }

        public function createArticleMethod(){ 

            $destination = $this->uploadFile();

            $article = [
                "title"=> addslashes($this->getPost("title_article")),
                "content"=>  addslashes($this->getPost("article_content")),
                "imgUrl"=> $destination,
                "imgAlt" => addslashes($this->getPost("article_content")),
                "createdAt"=> date("Y-m-d H:i:s")
            ];

            ModelFactory::getModel("Article")->createData($article);

            $this->setSession(["alert" => "success", "message" => "Votre article a été créé"]);

            $this->redirect("home");

    }

    public function editArticleMethod(){
        
       $article = $this->getArticleById();
       trim($article["content"]);
       
       return $this->twig->render("articles/editArticle.twig", [
           "article" => $article,
           "user" => $this->getSession()["user"]
       ]);
    }

    public function updateArticleMethod() {

        $existingArticle = $this->getArticleById();     
    
        if ($this->checkInputs()) {

            $updatedArticle = array_merge($existingArticle, $this->getPost());
       
            if (count($this->getFiles()) > 0) {
                if ($this->getFiles()["article_img"]["size"] > 0 && $this->getFiles()["article_img"]["size"] < 1000000) {
                    $this->deleteFile();
                    $destination = $this->uploadFile();
                    $updatedArticle["imgUrl"] = $destination;
                } else {
                    $updatedArticle["imgUrl"] = $existingArticle["imgUrl"];
                }
            }
    
            $updatedArticle["altImg"] = addslashes($this->getPost("content"));
            $updatedArticle["title"] = addslashes($updatedArticle["title"]);
            $updatedArticle["content"] = addslashes($updatedArticle["content"]);
            $updatedArticle["updatedAt"] = date("Y-m-d H:i:s");
    
            ModelFactory::getModel("Article")->updateData($updatedArticle["id"], $updatedArticle);
    
            $this->redirect("article_renderArticle", ["id" => $updatedArticle["id"]]);
        }
    }

    public function alertDeleteArticleMethod(){

        $this->setSession(["alert" => "danger", "message" => "Êtes-vous certain de vouloir supprimer cet article ?"]);

            return $this->twig->render("alert.twig", ["alert" => "danger", "message" => $this->getSession()["alert"]["message"], "article" => ModelFactory::getModel("Article")->readData($this->getGet("id"), "id")]);
    }

    public function deleteArticleMethod(){
       
        $id = $this->getGet()["id"];
        ModelFactory::getModel("Article")->deleteData($id);

        return $this->redirect("home");
        
    }
   

    public function uploadFile(){ 

        try {
        // Undefined | Multiple Files | $this->getFiles() Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($this->getFiles()['article_img']['error']) ||
            is_array($this->getFiles()['article_img']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }
    
        // Check $this->getFiles()['img']['error'] value.
        switch ($this->getFiles()['article_img']['error']) {
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
    
        // You should also check filesize here.
        if ($this->getFiles()['article_img']['size'] > 1000000) {
            throw new RuntimeException('Taille maiximale 1MB.');
        }
    
        // Check MIME Type by yourself.

        $fileMimeType = mime_content_type($this->getFiles()['article_img']['tmp_name']);
        $validMimeTypes = array(
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        );
    
        $ext = array_search($fileMimeType, $validMimeTypes, true);

        if ($ext ===  false) {
            return $this->setSession(["alert" => "danger", "message" => "Format invalide."]);
            // throw new RuntimeException('Invalid file format.');
        }

        $fileDestination = sprintf(
            './img/%s.%s',
            sha1_file($this->getFiles()['article_img']['tmp_name']), 
            $ext
        );

        // You should name it uniquely.
        // On this example, obtain safe unique name from its binary data.
        if (!move_uploaded_file(
            $this->getFiles()['article_img']['tmp_name'],
            $fileDestination
        )) {
    
            throw new RuntimeException('Il y a eu un problème lors du déplacement du fichier.');
        }

        // echo 'Votre photo a été importée avec succès.';
        return $fileDestination;

        }catch (RuntimeException $e) {
            echo $e->getMessage();
        }
  
    }

    public function deleteFile(){

        $imgPath = $this->getArticleById()["imgUrl"];

        if(file_exists($imgPath)){
            unlink($imgPath);
           return ;
        }

        return $this->setSession(["alert" => "danger", "message" => "Le fichier n'existe pas"]);
    }
}