<?php

namespace App\Controller;

class PublicationController extends MainController
{

    public function checkLog(){
        if(!isset($_SESSION['user'])){
          return  false;
        }

        return true;
    }

    public function createPublicationMethod(){

        if(!$this->checkLog()){
            $this->redirect("auth");
        }



    }

    
}