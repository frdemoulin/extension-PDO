<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}

/*
****************************************
insertion de données via requête directe
****************************************
*/

$bdd->exec('INSERT INTO jeux_video(nom, possesseur, console, prix, nbre_joueurs_max, commentaires) VALUES (\'Battlefield 1942\', \'Patrick\', \'PC\', 45, 50, \'2nde guerre mondiale\')');

echo 'OK ! Le jeu a bien été ajouté'

/*
*****************************
insertion de donnéesrequête préparée
*****************************
*/
// on prépare la requête en utilisant des marqueurs nominatifs
$req = $bdd->prepare('INSERT INTO jeux_video(nom, possesseur, console, prix, nbre_joueurs_max, commentaires) VALUES (:nom, :possesseur, :console, :prix, :nbre_joueurs_max, :commentaires)');
// on exécute la requête en insérant des valeurs stockées dans des variables (à affecter au préalable bien sûr)
$req->execute(array(
    'nom' => $nom,
    'possesseur' => $possesseur,
    'console' => $console,
    'nbre_joueurs_max' => $nbre_joueurs_max,
    'commentaires' => $commentaires
));
?>