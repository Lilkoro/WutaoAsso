<?php
ini_set('display_errors', 1); error_reporting(E_ALL);

require_once("../conf/connection.php");

// new-adh

// Démarrez la session
session_start();

// Vérifiez si le cookie accessToken est présent
if (!isset($_COOKIE['accessToken'])) {
    // Le cookie accessToken n'est pas présent, redirigez l'utilisateur vers la page de connexion
    header('Location: ../login');
    exit;
}

// Récupérez le token depuis le cookie
$tokenFromCookie = $_COOKIE['accessToken'];

// Lisez le contenu du fichier JSON
// Décodez le contenu JSON en tableau associatif
$jsonContent = file_get_contents('../login/log.json');
$credentials = json_decode($jsonContent, true);

// Vérifiez si la clé "accessToken" existe dans le tableau
if (isset($credentials['accessToken'])) {
    // Récupérez le token depuis le fichier JSON
    $tokenFromJSON = $credentials['accessToken'];

    // Vérifiez si le token du cookie correspond au token du fichier JSON
    if ($tokenFromCookie != $tokenFromJSON) {
        // Le token du cookie ne correspond pas au token du fichier JSON, redirigez l'utilisateur vers la page de connexion
        header('Location: ../login'.'?message=outofdate');
        exit;
    }
} else {
        // La clé "accessToken" n'existe pas dans le fichier JSON, redirigez l'utilisateur vers la page de connexion
        header('Location: ../login'.'?error=accessToken');
        exit;
    }
