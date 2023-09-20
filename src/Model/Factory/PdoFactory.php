<?php

namespace App\Model\Factory;

use PDO;

/**
 * Class PdoFactory
 * Creates the Connection if it doesn't exist
 * @package App\Model
 */
class PdoFactory
{

    /**
     * Stores the Connection
     * @var null
     */

    private static $pdo = null;

    /**
     * Returns the Connection if it exists or creates it before returning it
     * @return PDO|null
     */
    public  static function getPDO()
    {
        include_once "../config/db.php";

        if (self::$pdo === null) {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
            $pdo->exec("SET NAMES UTF8");
            self::$pdo = $pdo;
        }

        return self::$pdo;
    }

}
