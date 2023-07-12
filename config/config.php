<?php

// Configuration des paramètres de session
$sessionLifetime = 3600; // Durée de vie de la session en secondes
$sessionPath = '/'; // Chemin de la session (par défaut, utilisé pour les cookies)
$sessionDomain = 'blog-tristan.fr'; // Domaine de la session (facultatif, utilisé pour les cookies)
$sessionSecure = true; // Utiliser uniquement des cookies sécurisés (HTTPS)
$sessionHttpOnly = true; // Empêcher l'accès aux cookies via JavaScript

// Configuration des paramètres de cookie (optionnel)
session_set_cookie_params($sessionLifetime, $sessionPath, $sessionDomain, $sessionSecure, $sessionHttpOnly);
// $_SESSION["lifetime"] = $sessionLifetime;
// $_SESSION["path"] = $sessionPath;
// $_SESSION["domain"] = $sessionDomain;
// $_SESSION["secure"] = $sessionSecure;
// Démarrage de la session
session_start();
