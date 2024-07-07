<?php

// Fonction pour obtenir un token d'authentification
//require_once("./connection.php");

// Préparation de la requête

function getToken() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.helloasso.com/oauth2/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query(array(
            'grant_type' => 'client_credentials',
            'client_id' => 'XXXXXXXXXXXXXXXXX',
            'client_secret' => 'XXXXXXXXXXXXXXXXXXXXXXXXXX'
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);
    echo $response;
    curl_close($curl);
    $response_data = json_decode($response, true);
    return $response_data['access_token'];
}

// Fonction pour obtenir les slugs de formulaires en cours
function getFormSlug($token) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.helloasso.com/v5/organizations/WU-TAO-WANG-XUE-LONG/forms?states=Public&formTypes=MemberShip&pageIndex=1&pageSize=100',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}

// Fonction pour obtenir les informations des paiements d'un slug de formulaire spécifique
function getInfoCurrentSlug($token, $currentSlug) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.helloasso.com/v5/organizations/WU-TAO-WANG-XUE-LONG/forms/Membership/' . $currentSlug . '/payments?pageIndex=1&pageSize=50&sortOrder=Desc&sortField=Date',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}

function IdToInfo($id, $token){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.helloasso.com/v5/items/' . $id . '?withDetails=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ),
    ));
    
    $response = curl_exec($curl);

    curl_close($curl);
    return json_decode($response, true);
}

function detCat($dateNaissance){
    // Convertir la date de naissance en objet DateTime
    
    $dateNaissance = DateTime::createFromFormat('Y-m-d', $dateNaissance);

    // Vérifier si la date est valide
    if (!$dateNaissance) {
        return "Format de date invalide.";
    }

    // Obtenir la date actuelle
    $dateActuelle = new DateTime();

    // Calculer la différence
    $intervalle = $dateActuelle->diff($dateNaissance);

    // Retourner l'âge
    $age = $intervalle->y;
    if($age == 4 or $age  == 5)
    {
        return 1;
    }
    if($age == 6 or $age  == 7)
    {
        return 2;
    }
    if($age == 8 or $age  == 9)
    {
        return 3;
    }
    if($age == 10 or $age  == 11)
    {
        return 4;
    }
    if($age == 12 or $age  == 13)
    {
        return 5;
    }
    if($age == 14 or $age  == 15)
    {
        return 6;
    }
    if($age == 16 or $age  == 17)
    {
        return 7;
    }
    if($age >=18 and $age <= 39)
    {
        return 8;
    }
    if($age >= 40 and $age  <= 45)
    {
        return 9;
    }
    if($age >= 46 and $age <= 55)
    {
        return 10;
    }
    if($age >= 56 and $age <= 65)
    {
        return 11;
    }
    if($age >= 66)
    {
        return 12;
    }
}

function detLic($a){
    // dateInscr-MoisNaiss-AnneeNaiss-mb-Id
    $jsonContent = file_get_contents('../new-adh/conf.json');
    $info = json_decode($jsonContent, true);
    $mois = date("m");
    if($mois == "09") {
        $info["unique"] = 1; 
    }
    $id = $info["unique"];
    $year = date("Y");
    $listNaiss = explode("-", $a);
    $mois = $listNaiss[1];
    $annee = $listNaiss[0];
    $detLicence = $year.$mois.$annee."mb".$id;
    $info["unique"] = $id + 1;

    // Encodez le tableau en JSON
    $newJsonContent = json_encode($info, JSON_PRETTY_PRINT);

    // Écrivez le nouveau contenu dans le fichier JSON
    file_put_contents('../new-adh/conf.json', $newJsonContent);
    return $detLicence;
}

function reqconstruct($info,$sep){
    $constru = array();
    if($sep == "1"){
        if(isset($info["customFields"][5]["name"]) and $info["customFields"][4]["name"] == "Date de naissance de l'adhérent(e)"){
            $dateNaisse = $info["customFields"][5]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else {
            $dateNaiss = "";
            $numCat = 8;
            $licClub = "error";
        }
        if(isset($info["customFields"][1]["name"]) and $info["customFields"][1]["name"] == "Téléphone 2") {
            $tel2 = $info["customFields"][1]["answer"];
            $mail1 = $info["customFields"][2]["answer"];
        } else {
            $tel2 = null;
            $mail1 = $info["customFields"][1]["answer"];
        }  
        $nom = strtoupper($info["user"]["lastName"][0]).strtolower(substr($info["user"]["lastName"], 1, strlen($info["user"]["lastName"])));
        $prenom = strtoupper($info["user"]["firstName"][0]).strtolower(substr($info["user"]["firstName"], 1, strlen($info["user"]["firstName"])));
        $constru = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "DateNaiss" => $dateNaiss,
            "NumCat" => $numCat,
            "LicClub" => $licClub,
            "Tel1" => $info["customFields"][0]["answer"],
            "Mail1" => $info["customFields"][1]["answer"],
            "CodeJour" => 4,
            "CodeDisc" => 2,
            "SaisonArrive" => date("Y"),
        );

    } else if ( $sep == "2"){
        if(isset($info["customFields"][5]["name"]) and $info["customFields"][4]["name"] == "Date de naissance de l'adhérent(e)"){
            $dateNaisse = $info["customFields"][5]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else {
            $dateNaiss = "";
            $numCat = 8;
            $licClub = "error";
        }
        if(isset($info["customFields"][1]["name"]) and $info["customFields"][1]["name"] == "Téléphone 2") {
            $tel2 = $info["customFields"][1]["answer"];
            $mail1 = $info["customFields"][2]["answer"];
        } else {
            $tel2 = null;
            $mail1 = $info["customFields"][1]["answer"];
        }  
        $nom = strtoupper($info["user"]["lastName"][0]).strtolower(substr($info["user"]["lastName"], 1, strlen($info["user"]["lastName"])));
        $prenom = strtoupper($info["user"]["firstName"][0]).strtolower(substr($info["user"]["firstName"], 1, strlen($info["user"]["firstName"])));
        $constru = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "DateNaiss" => $dateNaiss,
            "NumCat" => $numCat,
            "LicClub" => $licClub,
            "Tel1" => $info["customFields"][0]["answer"],
            "Tel2" => $tel2,
            "Mail1" => $mail1,
            "CodeJour" => 4,
            "CodeDisc" => 1,
            "SaisonArrive" => date("Y"),
        );
    } else if ( $sep == "3"){
        if(isset($info["customFields"][5]["name"]) and $info["customFields"][4]["name"] == "Date de naissance de l'adhérent(e)"){
            $dateNaisse = $info["customFields"][5]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else {
            $dateNaiss = "";
            $numCat = 8;
            $licClub = "error";
        }  
        if(isset($info["customFields"][1]["name"]) and $info["customFields"][1]["name"] == "Téléphone 2") {
            $tel2 = $info["customFields"][1]["answer"];
            $mail1 = $info["customFields"][2]["answer"];
        } else {
            $tel2 = null;
            $mail1 = $info["customFields"][1]["answer"];
        }      
        $nom = strtoupper($info["user"]["lastName"][0]).strtolower(substr($info["user"]["lastName"], 1, strlen($info["user"]["lastName"])));
        $prenom = strtoupper($info["user"]["firstName"][0]).strtolower(substr($info["user"]["firstName"], 1, strlen($info["user"]["firstName"])));
        $constru = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "DateNaiss" => $dateNaiss,
            "NumCat" => $numCat,
            "LicClub" => $licClub,
            "Tel1" => $info["customFields"][0]["answer"],
            "Tel2" => $tel2,
            "Mail1" => $mail1,
            "CodeJour" => 4,
            "CodeDisc" => 3,
            "SaisonArrive" => date("Y"),
        );
    } else if ( $sep == "4"){
        if(isset($info["customFields"][2]["name"]) and $info["customFields"][2]["name"] == "Date de naissance de l'adhérent(e)"){
            $dateNaisse = $info["customFields"][2]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else if(isset($info["customFields"][3]["name"]) and $info["customFields"][3]["name"] == "Date de naissance de l'adhérent(e)"){
            $dateNaisse = $info["customFields"][3]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else if(isset($info["customFields"][4]["name"]) and $info["customFields"][4]["name"] == "Date de naissance de l'adhérent(e)"){
            $dateNaisse = $info["customFields"][4]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else {
            $dateNaiss = "";
            $numCat = 8;
            $licClub = "error";
        }

        if(isset($info["customFields"][1]["name"]) and $info["customFields"][1]["name"] == "Téléphone Tuteur 2") {
            $tel2 = $info["customFields"][1]["answer"];
            $mail1 = $info["customFields"][2]["answer"];
        } else {
            $tel2 = null;
            $mail1 = $info["customFields"][1]["answer"];
        }

        if(isset($info["customFields"][3]["name"]) and $info["customFields"][3]["name"] == "Mail Tuteur 2"){
            $mail2 = $info["customFields"][3]["answer"];
        }  else if (isset($info["customFields"][2]["name"]) and $info["customFields"][2]["name"] == "Mail Tuteur 2"){
            $mail2 = $info["customFields"][2]["answer"];
        } else { $mail2 = "";}

        if(isset($info["customFields"][3]["name"]) and $info["customFields"][3]["name"] == "Nom - prénom de l'enfant"){
            $a = explode(" ", $info["customFields"][3]["answer"]);
            $nom = $a[0]; $prenom = $a[1];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        } else if (isset($info["customFields"][4]["name"]) and $info["customFields"][4]["name"] == "Nom - prénom de l'enfant"){
            $a = explode(" ", $info["customFields"][4]["answer"]);
            $nom = $a[0]; $prenom = $a[1];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        } else if (isset($info["customFields"][5]["name"]) and $info["customFields"][5]["name"] == "Nom - prénom de l'enfant") {
            $a = explode(" ", $info["customFields"][5]["answer"]);
            $nom = $a[0]; $prenom = $a[1];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        } else {
            $nom = $info['user']['lastName']; $prenom = $info['user']['firstName'];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        }
        $constru = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "DateNaiss" => $dateNaiss,
            "NumCat" => $numCat,
            "LicClub" => $licClub,
            "Tel1" => $info["customFields"][0]["answer"],
            "Tel2" => $tel2,
            "Mail1" => $mail1,
            "Mail2" => $mail2,
            "CodeJour" => 3,
            "CodeDisc" => 1,
            "SaisonArrive" => date("Y"),
        ); 
    } else if ( $sep == "5"){
        if(isset($info["customFields"][2]["name"]) and $info["customFields"][2]["name"] == "Date naissance adhérent(e)"){
            $dateNaisse = $info["customFields"][2]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else if(isset($info["customFields"][3]["name"]) and $info["customFields"][3]["name"] == "Date naissance adhérent(e)"){
            $dateNaisse = $info["customFields"][3]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else if(isset($info["customFields"][4]["name"]) and $info["customFields"][4]["name"] == "Date naissance adhérent(e)"){
            $dateNaisse = $info["customFields"][4]["answer"];
            $a = explode("/", $dateNaisse);
            $dateNaiss = $a[2]."-".$a[1]."-".$a[0];
            $numCat = detCat($dateNaiss);
            $licClub = detLic($dateNaiss);
        } else {
            $dateNaiss = "";
            $numCat = 8;
            $licClub = "error";
        }
        if(isset($info["customFields"][1]["name"]) and $info["customFields"][1]["name"] == "Téléphone tuteur 2") {
            $tel2 = $info["customFields"][1]["answer"];
            $mail1 = $info["customFields"][2]["answer"];
        } else {
            $tel2 = "";
            $mail1 = $info["customFields"][1]["answer"];
        }

        if(isset($info["customFields"][3]["name"]) and $info["customFields"][3]["name"] == "Mail Tuteur 2"){
            $mail2 = $info["customFields"][3]["answer"];
        } else if (isset($info["customFields"][2]["name"]) and $info["customFields"][2]["name"] == "Mail Tuteur 2"){
            $mail2 = $info["customFields"][2]["answer"];
        }  else { $mail2 = "";}
        if(isset($info["customFields"][3]["name"]) and $info["customFields"][3]["name"] == "Choix du jour d'entrainement"){

            $jour = $info["customFields"][3]["answer"];
            if($jour ==" jeudi"){
                $codeJour = 4;
            } else {
                $codeJour = 3;
            }
        } else if(isset($info["customFields"][4]["name"]) and $info["customFields"][4]["name"] == "Choix du jour d'entrainement"){

            $jour = $info["customFields"][4]["answer"];
            if($jour ==" jeudi"){
                $codeJour = 4;
            } else {
                $codeJour = 3;
            }
        } else if(isset($info["customFields"][5]["name"]) and $info["customFields"][5]["name"] == "Choix du jour d'entrainement"){
            $jour = $info["customFields"][5]["answer"];
            if($jour ==" jeudi"){
                $codeJour = 4;
            } else {
                $codeJour = 3;
            }
        }

        if(isset($info["customFields"][4]["name"]) and $info["customFields"][4]["name"] == "Nom - prénom de l'enfant"){
            $a = explode(" ", $info["customFields"][4]["answer"]);
            $nom = $a[0]; $prenom = $a[1];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        } else if (isset($info["customFields"][5]["name"]) and $info["customFields"][5]["name"] == "Nom - prénom de l'enfant"){
            $a = explode(" ", $info["customFields"][5]["answer"]);
            $nom = $a[0]; $prenom = $a[1];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        } else if (isset($info["customFields"][6]["name"]) and $info["customFields"][6]["name"] == "Nom - prénom de l'enfant") {
            $a = explode(" ", $info["customFields"][6]["answer"]);
            $nom = $a[0]; $prenom = $a[1];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        } else {
            $nom = $info['user']['lastName']; $prenom = $info['user']['firstName'];
            $nom = $nom[0].strtolower(substr($nom, 1, strlen($nom))); $prenom = $prenom[0].strtolower(substr($prenom, 1, strlen($prenom))); 
        }
        $constru = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "DateNaiss" => $dateNaiss,
            "NumCat" => $numCat,
            "LicClub" => $licClub,
            "Tel1" => $info["customFields"][0]["answer"],
            "Tel2" => $tel2,
            "Mail1" => $mail1,
            "Mail2" => $mail2,
            "CodeJour" => $codeJour,
            "CodeDisc" => 1,
            "SaisonArrive" => date("Y"),
        );
    }
    return $constru;
}

function reqsend($tabl, $stmt){
    // Liaisons des paramètres avec les valeurs du tableau associatif
    foreach ($tabl as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    // Exécution de la requête
    $stmt->execute();
}
// Fonction principale pour mettre à jour la base de données

try {
    // Obtenir le token d'authentification
    $token = getToken();

    // Obtenir les slugs de formulaires en cours
    $result1 = getFormSlug($token);
    $formSlug = $result1['data'];
    $listSlug = [];
    $listItemsId = [];
    $nom1 = [];

    // Ajouter tous les slugs à la liste
    foreach ($formSlug as $slug) {
        $listSlug[] = $slug['formSlug'];
    }
    // Pour chaque slug, obtenir les informations des paiements et remplir les listes des ID et noms

    foreach ($listSlug as $currentSlug) {
        $result2 = getInfoCurrentSlug($token, $currentSlug);

        foreach ($result2['data'] as $data) {
            $listItemsId[] = $data['items'][0]['id'];
        }
        foreach($listItemsId as $id){
                $info = IdToInfo($id,$token);
                if(isset($info["customFields"])){ 
                    if(!(strtolower($info["payer"]["lastName"]) == "michaud" and strtolower($info["payer"]["firstName"]) == "christian")){
                        if($info["order"]["formSlug"] == "adhesion-pack-tai-chi-plus-kung-fu-de-l-ecole-wu-tao-wang-xue-long-2"){
                            $sql = "
                            INSERT INTO adherent (nom, prenom, DateNaiss, NumCat, LicClub, Tel1, Mail1, CodeJour, CodeDisc, SaisonArrive)
                            SELECT :nom, :prenom, :DateNaiss, :NumCat, :LicClub, :Tel1, :Mail1, :CodeJour, :CodeDisc, :SaisonArrive
                            WHERE NOT EXISTS (
                            SELECT 1 FROM adherent WHERE nom = :nom AND prenom = :prenom
                            );";
                            // Préparation de la requête avec PDO
                            $stmt = $con->prepare($sql);
                            $request = reqconstruct($info,"1");
                            reqsend($request, $stmt);
                        } else if($info["order"]["formSlug"] == "adhesion-aux-cours-kung-fu-adultes-de-l-ecole-wu-tao-wang-xue-long-2-1") {
                            $sql = "
                            INSERT INTO adherent (nom, prenom, DateNaiss, NumCat, LicClub, Tel1, Tel2, Mail1, CodeJour, CodeDisc, SaisonArrive)
                            SELECT :nom, :prenom, :DateNaiss, :NumCat, :LicClub, :Tel1, :Tel2, :Mail1, :CodeJour, :CodeDisc, :SaisonArrive
                            WHERE NOT EXISTS (
                            SELECT 1 FROM adherent WHERE nom = :nom AND prenom = :prenom
                            );";

                            // Préparation de la requête avec PDO
                            $stmt = $con->prepare($sql);
                            $request = reqconstruct($info,"2");
                            reqsend($request, $stmt);
                        } else if($info["order"]["formSlug"] == "adhesion-pack-tai-chi-et-kung-fu") {
                            $sql = "
                            INSERT INTO adherent (nom, prenom, DateNaiss, NumCat, LicClub, Tel1, Tel2, Mail1, CodeJour, CodeDisc, SaisonArrive)
                            SELECT :nom, :prenom, :DateNaiss, :NumCat, :LicClub, :Tel1, :Tel2, :Mail1, :CodeJour, :CodeDisc, :SaisonArrive
                            WHERE NOT EXISTS (
                            SELECT 1 FROM adherent WHERE nom = :nom AND prenom = :prenom
                            );";

                            // Préparation de la requête avec PDO
                            $stmt = $con->prepare($sql);
                            $request = reqconstruct($info,"3");
                            reqsend($request, $stmt);
                        } else if($info["order"]["formSlug"] == "adhesion-cours-adolescents-11-15-ans") {
                            $sql = "
                            INSERT INTO adherent (nom, prenom, DateNaiss, NumCat, LicClub, Tel1, Tel2, Mail1, Mail2, CodeJour, CodeDisc, SaisonArrive)
                            SELECT :nom, :prenom, :DateNaiss, :NumCat, :LicClub, :Tel1, :Tel2, :Mail1, :Mail2, :CodeJour, :CodeDisc, :SaisonArrive
                            WHERE NOT EXISTS (
                            SELECT 1 FROM adherent WHERE nom = :nom AND prenom = :prenom
                            );";

                            // Préparation de la requête avec PDO
                            $stmt = $con->prepare($sql);
                            $request = reqconstruct($info,"4");
                            reqsend($request, $stmt);
                        } else if($info["order"]["formSlug"] == "adhesion-cours-enfants-5-11-ans") {
                            // echo $info["order"]["id"]." |-| "."<pre>".print_r($info["customFields"],true)."</pre>"." </br>";
                            $sql = "
                            INSERT INTO adherent (nom, prenom, DateNaiss, NumCat, LicClub, Tel1, Tel2, Mail1, Mail2, CodeJour, CodeDisc, SaisonArrive)
                            SELECT :nom, :prenom, :DateNaiss, :NumCat, :LicClub, :Tel1, :Tel2, :Mail1, :Mail2, :CodeJour, :CodeDisc, :SaisonArrive
                            WHERE NOT EXISTS (
                            SELECT 1 FROM adherent WHERE nom = :nom AND prenom = :prenom
                            );";
                            // Préparation de la requête avec PDO
                            $stmt = $con->prepare($sql);
                            $request = reqconstruct($info,"5");
                            reqsend($request, $stmt);
                        }
                    }
                }
        }
    }
} catch (Exception $e) {
    echo 'ERROR:';
    echo $e->getMessage();
}
