<?php

namespace App\Controller;

/**
 * Class GlobalsController
 * @package App\Controller
 */
abstract class GlobalsController
{
    /**
     * @var array
     */

    private $alert = [];

    /**
     * @var array
     */

    private $env = [];

    /**
     * @var array
     */

    private $file = [];

    /**
     * @var array
     */

    private $files = [];

    /**
     * @var array
     */

    private $get = [];

    /**
     * @var array
     */

    private $post = [];

    /**
     * @var array
     */

    private $request = [];

    /**
     * @var array
     */

    private $server = [];

    /**
     * @var array
     */

    private $session = [];

    /**
     * @var array
     */

    private $user = [];

    /**
     * GlobalsController Constructor
     * Assign all Globals to Properties
     * With some Checking for Files & Session
     */
    public function __construct()
    {

        $this->env      = filter_input_array(INPUT_ENV) ?? [];
        $this->get      = filter_input_array(INPUT_GET) ?? [];
        $this->post     = filter_input_array(INPUT_POST) ?? [];
        $this->server   = filter_input_array(INPUT_SERVER) ?? [];

        $this->files    = filter_var_array($_FILES) ?? [];
        $this->request  = filter_var_array($_REQUEST) ?? [];

        if (isset($this->files["file"]) === TRUE) {
            $this->file = $this->files["file"];
        }

        if (array_key_exists("alert", $_SESSION) === false) {
            $_SESSION["alert"] = [];
        }

        $this->session  = filter_var_array($_SESSION) ?? [];
        $this->alert    = $this->session["alert"];

        if (isset($this->session["user"]) === TRUE) {
            $this->user = $this->session["user"];
        }

    }


    /**
     * Set User Session or User Alert
     * @param array $user User information
     * @param bool $session Alert information
     * @return array|void
     */
    protected  function setSession(array $user, bool $session=false)
    {
        if ($session === false) {
            $_SESSION["alert"] = $user;
        } elseif ($session === true) {
            if (isset($user["pass"]) === TRUE) {
                unset($user["pass"]);
            } elseif (isset($user["password"]) === TRUE) {
                unset($user["password"]);
            }
            $_SESSION["user"] = $user;
        }
    }


    // ******************** CHECKERS ******************** \\
    /**
     * Check User Alert or User Session
     * @param bool $alert
     * @return bool
     */
    protected function checkUser(bool $alert=FALSE)
    {
        if ($alert === TRUE) {
            return empty($this->alert) === FALSE;
        }

        if (array_key_exists("user", $this->session) === TRUE) {
            if (empty($this->user) === FALSE) {
                return true;
            }
        }

        return false;
    }


    protected function checkInputs()
    {
        $inputs = $this->getPost();

        foreach ($inputs as $input => $value) {
            if (empty($value) === TRUE) {
                $this->setSession([
                    "alert" => "danger",
                    "message" => "Veuillez remplir le champ" . $input
                ]);
                return false;
            }
        }

        return true;
    }


    // ******************** GETTERS ******************** \\
    /**
     * Get Alert Type or Alert Message
     * @param bool $type
     * @return string|void
     */
    protected function getAlert(bool $type=false)
    {
        if (isset($this->alert) === TRUE) {
            if ($type === TRUE) {
                return $this->alert["type"] ?? "";
            }

            echo filter_var($this->alert["message"]);
            unset($_SESSION["alert"]);
        }
    }


    /**
     * Get Env Array or Env Var
     * @param null|string $var
     * @return array|string
     */
    protected function getEnv(string $var=null)
    {
        if ($var === null) {
            return $this->env;
        }

        return $this->env[$var] ?? "";
    }


    /**
     * Get Files Array, File Array or File Var
     * @param null|string $var
     * @return array|string
     */
    protected function getFiles(string $var=null)
    {
        if ($var === null) {
            return $this->files;
        }

        if ($var === "file") {
            return $this->file;
        }

        return $this->file[$var] ?? "";
    }


    /**
     * Get Get Array or Get Var
     * @param null|string $var
     * @return array|string
     */
    protected function getGet(string $var = null)
    {
        if ($var === null) {
            return $this->get;
        }

        return $this->get[$var] ??"";
    }


    /**
     * Get Post Array or Post Var
     * @param null|string $var
     * @return array|string
     */
    protected function getPost(string $var = null)
    {
        if($var === null) {
            return $this->post;
        }

        return $this->post[$var] ??"";
    }


    /**
     * Get Request Array or Request Var
     * @param null|string $var
     * @return array|string
     */
    protected function getRequest(string $var = null)
    {
        if($var === null) {
            return $this->request;
        }

        return $this->request[$var] ??"";
    }


    /**
     * Get Server Array or Server Var
     * @param null|string $var
     * @return array|string
     */
    protected function getServer(string $var = null)
    {
        if ($var === null) {
            return $this->server;
        }

        return $this->server[$var] ??"";
    }


    /**
     * Get Session Array, User Array or User Var
     * @param null|string $var
     * @return array|string
     */
    protected function getSession(string $var = null)
    {
        if ($var === null) {
            return $this->session;
        }

        if ($var ==="user") {
            return $this->user;
        }

        if (!$this->checkUser()) {
            $this->user[$var] = null;
        }

        return $this->user[$var] ??"";
    }


    // ******************** DESTROYER ******************** \\
    /**
     * Destroy $name Cookie or Current Session
     * @param string $name
     */


    protected function destroyGlobal(/*string $name = null*/)
    {
        $_SESSION["user"] = [];
        session_destroy();
    }

     // Fichiers!
    /**
     * Uploads a file.
     * @throws RuntimeException if there are invalid parameters, file size is too large, MIME type is invalid, or there is an error moving the file.
     * @return string the file destination on success.
     */
    public function uploadFile()
    { 

        try {
            // Undefined | Multiple Files | $this->getFiles() Corruption Attack!
            // If this request falls under any of them, treat it invalid!
                if (!isset($this->getFiles()['img']['error']) || is_array($this->getFiles()['img']['error'])) {
                    throw new RuntimeException('Invalid parameters.');
                }
            

            $this->checkFileError();

            // You should also check filesize here!
            if ($this->getFiles()['img']['size'] > 1000000) {
                throw new RuntimeException('Taille maiximale 1MB.');
            }

            $ext = $this->checkFileMime();

            $fileDestination = sprintf(
                './img/%s.%s',
                sha1_file($this->getFiles()['img']['tmp_name']),
                $ext
            );

            // You should name it uniquely!
            // On this example, obtain safe unique name from its binary data!
            if (move_uploaded_file($this->getFiles()['img']['tmp_name'], $fileDestination) === FALSE) {
                throw new RuntimeException('Il y a eu un problème lors du déplacement du fichier.');
            }

            return $fileDestination;

        } catch (RuntimeException $e) {
                echo $e->getMessage();
        }
    }


    /**
     * Checks if there is any error with the uploaded file.
     * @throws RuntimeException if there is no file uploaded.
     * @throws RuntimeException if the file size exceeds the maximum allowed (1MB).
     * @throws RuntimeException if an unidentified error occurs.
     */
    private function checkFileError()
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
    private function checkFileMime()
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


    /**
     * Deletes a file.
     * @throws Some_Exception_Class If the file does not exist
     * @return void
     */
    private function deleteFile()
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


    /**
     * Updates the file.
     * @return string|null The destination of the uploaded file, or null if the file size is invalid.
     */
    private function updateFile()
    {
        if ($this->getFiles()["img"]["size"] > 0 && $this->getFiles()["img"]["size"] < 1000000) {
            $this->deleteFile();
            $destination = $this->uploadFile();

            return $destination;
        }

    }


}
