<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class MainController
 * Manages the Main Features
 * @package App\Controller
 */
abstract class MainController extends GlobalsController
{
    /**
     * @var Environment|null
     */
    protected $twig = null;

    /**
     * MainController constructor
     * Creates the Template Engine & adds its Extensions
     */
    public function __construct()
    {
    
        parent::__construct();
        $this->twig = new Environment(new FilesystemLoader("../src/View"), [
            "cache" => false
        ]);
    
    }


    /**
     * Redirects to another URL
     * @param string $page
     * @param array $params
     * */ 
    public function redirect(string $page, array $params = [])
    {

        $params["access"] = $page;
        $redirectUrl = "index.php?" . http_build_query($params);

        return $redirectUrl;

    }


    // Public function redirect(string $page, array $params = [])
    // {
    // $params["access"] = $page;
    // header("Location: index.php?".  htmlspecialchars(http_build_query($params)));
    // exit;
    // }
}
