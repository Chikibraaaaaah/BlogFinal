<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use RuntimeException;
class ArticleController extends MainController
{

  private $titre;
  private $contenu;
  private $img;
  private $datePublication;

   public function defaultMethod(){
    
   }

   public function createArticleMethod(){ 



    $destination = $this->uploadFile();


    $article = [
      "titre"=> $this->getPost("titre"),
      "contenu"=> $this->getPost("article"),
      "imgUrl"=> $destination,
      "altImg" => $this->getPost("article"),
      "datePublication"=> date("Y-m-d H:i:s")
    ];



    ModelFactory::getModel("Article")->createData($article);

    $this->setSession(["alert" => "success", "message" => "Votre article a été créé"]);

    $this->redirect("home");

   }

   public function uploadFile(){ 

    try {
      // Undefined | Multiple Files | $this->getFiles() Corruption Attack
      // If this request falls under any of them, treat it invalid.
      if (
          !isset($this->getFiles()['img']['error']) ||
          is_array($this->getFiles()['img']['error'])
      ) {
          throw new RuntimeException('Invalid parameters.');
      }
  
      // Check $this->getFiles()['img']['error'] value.
      switch ($this->getFiles()['img']['error']) {
          case UPLOAD_ERR_OK:
              break;
          case UPLOAD_ERR_NO_FILE:
              throw new RuntimeException('No file sent.');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
              throw new RuntimeException('Exceeded filesize limit.');
          default:
              throw new RuntimeException('Unknown errors.');
      }
  
      // You should also check filesize here.
      if ($this->getFiles()['img']['size'] > 1000000) {
          throw new RuntimeException('Exceeded filesize limit.');
      }
  
      // Check MIME Type by yourself.

      $fileMimeType = mime_content_type($this->getFiles()['img']['tmp_name']);
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

      $fileDestination = sprintf('./img/%s.%s',
      sha1_file($this->getFiles()['img']['tmp_name']), 
      $ext
    );

      // You should name it uniquely.
      // On this example, obtain safe unique name from its binary data.
      if (!move_uploaded_file(
          $this->getFiles()['img']['tmp_name'],
          $fileDestination
      )) {
  
          throw new RuntimeException('Failed to move uploaded file.');
      }
      // echo "<pre>";
      // print_r($this->getFiles()["img"]["tmp_name"]);
      // echo "</pre>";


      echo 'File is uploaded successfully.';
      return $fileDestination;


  
  }catch (RuntimeException $e) {
      echo $e->getMessage();
  }
  

}
}