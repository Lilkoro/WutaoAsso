<?php
    $jsonContent = file_get_contents('../login/log.json');
    $credentials = json_decode($jsonContent, true);

    // Vérifiez si les clés "utilisateur" et "motDePasse" existent dans le tableau
    if (isset($credentials['user'], $credentials['password'])) {
        // Récupérez les informations de connexion
        $nomUtilisateurAttendu = $credentials['user'];
        $motDePasseAttendu = $credentials['password'];

        // Vérifiez les informations d'authentification (à adapter selon votre système d'authentification)
        $nomUtilisateur = $_POST['user'] ?? '';
        $motDePasse = $_POST['password'] ?? '';

        if ($nomUtilisateur === $nomUtilisateurAttendu && $motDePasse === $motDePasseAttendu) {
            // Authentification réussie
            $jetonAcces = bin2hex(random_bytes(32)); // Exemple de génération de jeton simple

            // Stockez le jeton d'accès dans un cookie avec une durée de vie de 1 heure (3600 secondes)
            setcookie('accessToken', $jetonAcces, time() + 3600, '/');

            // Ajoutez les informations d'authentification au tableau
            $credentials['accessToken'] = $jetonAcces;

            // Encodez le tableau en JSON
            $newJsonContent = json_encode($credentials, JSON_PRETTY_PRINT);

            // Écrivez le nouveau contenu dans le fichier JSON
            file_put_contents('../login/log.json', $newJsonContent);

            $redirectUrl = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : '../accueil';
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            // Authentification échouée
            // Redirigez l'utilisateur vers l'URL d'origine avec un paramètre d'erreur
            $redirectUrl = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : './';
            header('Location: https://marc-kineider.xyz/wutao/accueil?erreur');
            exit;
        }
    } else {
        // Le fichier JSON ne contient pas les clés attendues
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Configuration JSON invalide';
    }
?>
