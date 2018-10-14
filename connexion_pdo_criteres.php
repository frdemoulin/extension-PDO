<?php

/*
L'objet PDO prend quatre paramètres :
1) le nom d'hôte (plus précisément, DSN pour Data Source Name)
2) la base de données
3) le login
4) le mot de passe
5) activation des erreurs SQL
*/

// on entre dans le bloc try
try {
    // on définit l'objet connexion à la base sql
    $bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}

// si erreur à la connexion, PDO renvoie une exception et on entre dans le bloc catch
// cela évite d'afficher un message d'erreur (contenant par exemple le mot de passe) en cas de pbl à la connexion à sql
catch (Exception $e)
{
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}

// Si pas d'erreur à la connexion à la base, 
// on envoie la requête de php à mysql

// on sélectionne les champs nom de la table jeux_video
// on récupère la réponse de la bdd dans l'objet $reponse
// $reponse contient tout le résultat de la requête
$reponse = $bdd->query('SELECT nom, possesseur, console, prix FROM jeux_video WHERE console=\'Xbox\' OR console=\'PS2\' ORDER BY prix DESC LIMIT 0,10');

// on extrait la réponse ligne par ligne dans l'array $donnees
// $donnees est un array qui contient champ par champ les valeurs de la 1e entrée
// exemple : pour extraire le champ console, array $donnees['console']

// on boucle sur l'array $donnees afin de balayer toutes les entrées de la table jeux_video
// on récupère une nouvelle entrée et on place son contenu dans $donnees
// si $donnees vaut vrai (ie si l'entrée suivante dans la table jeux_video est non vide), on entre dans la boucle
// le fetch renvoie false dans $donnes lorsque toute la table a été balayée 
// attention, pas de ; après fetch()

while ($donnees = $reponse->fetch()) {

// on affiche les résultats contenus dans l'array $donnees
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>
        <?= $donnees['nom']; ?> sur <?= $donnees['console'] ?> coûte <?= $donnees['prix']; ?> euros <br />
    </p>
<?php 
}

// termine le traitement de la requête sql
// fermeture du curseur d'analyse des résultats
$reponse ->closeCursor();

?>
</body>
</html>
