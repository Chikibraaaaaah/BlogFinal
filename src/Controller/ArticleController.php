<?php

namespace App\Controller;

use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;

class ArticleController extends MainController
{


    /**
     * A description of the defaultMethod PHP function
     * @return $article
     */
    public function defaultMethod()
    {
        $id = $this->getGet("id");

        $article = ModelFactory::getModel("Article")->readData($id, "id");
        $this->redirect("article_renderArticle", [
            "id" => $id
        ]);
    }


    // Render functions!
    /**
     * Render the article method.
     * @return mixed
     */
    public function renderArticleMethod()
    {
        $article = ModelFactory::getModel("Article")->readData($this->getGet("id"), "id");
        $relatedComments = (ModelFactory::getModel("Comment")->listData($article["id"], "articleId")) ?? [];
        $alerts = ($this->getAlert(true)) ?? [];
        $user = ($this->getSession()["user"]) ?? [];

        return $this->twig->render("articles/articleSingle.twig", [
            "user" => $user,
            "article" => $article,
            "comments" => $relatedComments,
            "alerts" => $alerts,
            "method" => "GET"
        ]);
    }

    public function addArticleMethod(){

        $user = $this->getSession("user");

        return $this->twig->render("articles/articleCreate.twig", ["user" => $user]);

    }


    /**
     * Modify the article method.
     * @return string The rendered article single view.
     */
    public function modifyArticleMethod()
    {
        $id = $this->getGet("id");
        $article = ModelFactory::getModel("Article")->readData($id, "id");
        $relatedComments = ModelFactory::getModel("Comment")->listData($article["id"], "articleId");

        return $this->twig->render("articles/articleSingle.twig", [
            "user" => $this->getSession("user"),
            "article" => $article,
            "method" => "PUT",
            "comments" => $relatedComments
        ]);
    }


    // CRUD functions!
    /**
     * Create an article method.
     * Uploads a file and creates an article with the given title, content, image URL,
     * image alt, and creation date. The article is then saved using the ModelFactory
     * and a success alert message is set in the session. Finally, the user is redirected
     * to the home page.
     * @return void
     */
    public function createArticleMethod()
    {
        $destination = $this->uploadFile();
        $article = [
            "title"     => $this->encodeString($this->getPost("title")),
            "content"   => $this->encodeString($this->getPost("content")),
            "imgUrl"    => $destination,
            "imgAlt"    => $this->encodeString($this->getPost("alt")),
            "createdAt" => date("Y-m-d H:i:s")
        ];

        ModelFactory::getModel("Article")->createData($article);
        $this->setSession([
            "alert" => "success",
            "message" => "Votre article a été créé"
        ]);
        $this->redirect("home");
    }



    /**
     * Retrieves an article by its ID.
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function getArticleById()
    {
        $articleId = $this->getGet("id");
        $article = ModelFactory::getModel("Article")->readData($articleId,"id");

        return $article;
    }


    /**
     * Update an article.
     * This function updates an existing article by merging the existing article
     * with the new post data. It also handles the uploading of a new image if
     * one is provided, and updates the image URL accordingly. The function then
     * adds slashes to the content, title, and updated content fields to escape
     * any special characters. Finally, the function updates the article data in
     * the database using the Article model, and returns the rendered article.
     * @return mixed The rendered article.
     */
    public function updateArticleMethod()
    {
        $existingArticle = $this->getArticleById();
        $destination = $existingArticle["imgUrl"];

        if ($this->getFiles()["img"]["size"] > 0 && $this->getFiles()["img"]["size"] < 1000000) {
            $destination = $this->updateFile();
        }

        if ($this->checkInputs() === TRUE) {
            $updatedArticle = array_merge($existingArticle, $this->getPost());
            $updatedArticle["imgUrl"] = $destination;
            $updatedArticle["imgAlt"]       = $this->encodeString($this->getPost("content"));
            $updatedArticle["title"]        = $this->encodeString($updatedArticle["title"]);
            $updatedArticle["content"]      = $this->encodeString($updatedArticle["content"]);
            $updatedArticle["updatedAt"]    = date("Y-m-d H:i:s");

            ModelFactory::getModel("Article")->updateData((int) $updatedArticle["id"], $updatedArticle);

            $this->redirect("article_renderArticle", [
                "id" => (int) $updatedArticle["id"]
            ]);
        }
    }


    /**
     * Deletes an article.
     * @throws Some_Exception_Class If the article cannot be deleted.
     * @return void
     */
    public function deleteArticleMethod()
    {
        $id = $this->getGet()["id"];

        ModelFactory::getModel("Article")->deleteData($id);
        $this->redirect("home");
    }

    public function deleteFile()
    {
        $imgPath = $this->getArticleById()["imgUrl"];

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
