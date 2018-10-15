<?php

/*
*******************************
CONNEXION À UNE BDD AVEC MYSQLI
*******************************

1) Ouverture d'une connexion à MySQL et sélection de la base de données
2) Requête sur la base de données
3) Exploitation des résultats de la requête
4) Fermeture de la connexion à MySQL
*/

/*
-----------------------------------------------------------------------
1) Ouverture d'une connexion à MySQL et sélection de la base de données
-----------------------------------------------------------------------
*/

$serveur = "mysql.monserveursql.com";
$base = "mabase";
$user = "monuser";
$pass = "monpassword";


// $mysqli est une nouvelle instance de la classe mysqli
// prédéfinie dans php et hérite donc de ses propriétés et méthodes

// connexion à la base de données
$mysqli = new mysqli($serveur, $user, $pass, $base);

// si la connexion se fait en UTF-8, sinon ne rien indiquer
$mysqli->set_charset("utf8");

// utilisation de la méthode connect_error qui renvoie un message d'erreur si la connexion échoue

if ($mysqli->connect_error) {
    die('Erreur de connexion ('.$mysqli->connect_errno.')'. $mysqli->connect_error);
}

/*
-----------------------------------------------------------------------
2) Requête sur la base de données
-----------------------------------------------------------------------
*/

// requête de php à mysql
// on sélectionne tout le contenu de la table jeux_video
// on récupère la réponse de la bdd dans l'objet $reponse
// $reponse contient toute la table jeux_video
$reponse = $bdd->query('SELECT * FROM jeux_video');

/*
-----------------------------------------------------------------------
3) Exploitation des résultats de la requête
-----------------------------------------------------------------------
*/

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
        <strong>Jeu <?= $donnees['ID']; ?> :</strong> <?= $donnees['nom']; ?><br />

        Le possesseur de ce jeu est <?= $donnees['possesseur']; ?>, il le vend à <?= $donnees['prix']; ?> euros !<br />

        Ce jeu fonctionne sur <?= $donnees['console']; ?> et on peut y jouer à <?= $donnees['nbre_joueurs_max']; ?> au maximum<br />

        <?= $donnees['possesseur']; ?> a laissé ces commentaires sur <?= $donnees['nom']; ?> : <em><?= $donnees['commentaires']; ?> </em>
    </p>
<?php 
}

/*
-----------------------------------------------------------------------
4) Fermeture de la connexion à MySQL
-----------------------------------------------------------------------
*/

// on s'assure de la fermeture de la connexion avec la méthode close()
$mysqli->close();

?>
</body>
</html>
