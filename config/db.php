<?php

// Data Base name!
define("DB_DSN", "mysql:host=localhost;dbname=blog");

// Define user!
define("DB_USER", "root");

// Password!
define("DB_PASS", "root");

// Define database options!
define("DB_OPTIONS", [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
