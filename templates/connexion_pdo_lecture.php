<?php

/*
*************************************************
CONNEXION À UNE BDD AVEC PDO - REQUÊTE EN LECTURE
*************************************************

1) Ouverture d'une connexion à MySQL et sélection de la base de données
2) Requête sur la base de données
3) Exploitation des résultats de la requête
4) Fermeture de la connexion à MySQL
*/

/*
L'objet PDO prend 4 paramètres :
1. DSN : nom d'hôte + nom de base de données (+ éventuellement charset)
2. nom d'utilisateur
3. mot de passe
4. activation des erreurs PDO
*/

// DSN pour Data Source Name
// exemple sans charset : 'mysql:host=localhost;dbname=test;'
// exemple avec charset : 'mysql:host=localhost;dbname=test;charset=utf8'

/* 
*********************
ERREURS DE CONNEXION
*********************

Par défaut, PDO est configuré en mode silencieux (il ne rapportera pas les erreurs). Il existe trois modes d'erreurs :
PDO::ERRMODE_SILENT - ne rapporte pas d'erreur (mais assignera les codes d'erreurs)
PDO::ERRMODE_WARNING - émet un warning
PDO::ERRMODE_EXCEPTION - lance une exception
/*

/*
-----------------------------------------------------------------------
1) Ouverture d'une connexion à MySQL et sélection de la base de données
-----------------------------------------------------------------------
*/

// déclaration des variables de connexion
$host = 'localhost';
$dbname = 'test';
$user = 'root';
$password = '';

// on entre dans le bloc try
try {
    // on instancie un objet $bdd de la classe PDO représentant la connexion entre PHP et MySQL
    // on active le mode d'erreur PDO::ERRMODE_EXCEPTION (lance une exception)
    $bdd = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    // version sans variable
    // $bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}

// si erreur à la connexion, PDO renvoie une exception et on entre dans le bloc catch
// cela évite d'afficher un message d'erreur contenant par exemple le mot de passe en cas de pbl à la connexion à sql
catch (Exception $e)
{
    // en cas d'erreur, on affiche un message et on arrête tout

    // on appelle 3 méthodes de l'objet de la classe PDOException créé lors de l'échec à la connexion
    // getFile() : récupère le fichier dans lequel l'exception a été créée
    // getLine() : récupère la ligne dans laquelle l'exception a été créée
    // getMessage() : récupère le message de l'exception
    // la fonction die() affiche le message d'erreur et stoppe le script PHP
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}

// Si pas d'erreur à la connexion à la base, on envoie la requête de php à mysql

/*
-----------------------------------------------------------------------
2) Requête sur la base de données
-----------------------------------------------------------------------
*/

// requête de php à mysql
// requête non préparée à l'aide de la méthode query() de la classe PDO
// -> signifie que l'on appelle une méthode sur un objet : $objet->methode()
// query() est utilisée pour les requêtes qui interrogent la base de données (type SELECT)
// exemple : on sélectionne tout le contenu de la table jeux_video
// on récupère la réponse de la bdd dans l'objet $reponse
// $reponse contient toute la table jeux_video
$reponse = $bdd->query('SELECT * FROM jeux_video');

/*
-----------------------------------------------------------------------
3) Exploitation des résultats de la requête
-----------------------------------------------------------------------
*/

// on extrait la réponse ligne par ligne dans l'array $donnees à l'aide de la méthode fetch() de la classe PDOstatement
// fetch() : renvoie dans un array l’enregistrement correspondant à une entrée d'un jeu de résultats PDO, puis place le curseur sur l’enregistrement suivant
// $donnees est un array qui contient champ par champ les valeurs des entrées
// exemple : pour extraire le champ console, $donnees['console']

// on boucle (while) sur l'array $donnees afin de balayer toutes les entrées de la table
// on récupère une nouvelle entrée et on place son contenu dans $donnees

// $reponse->fetch() vaut true si l'entrée suivante dans la table est non vide, on entre alors dans la boucle
// $reponse->fetch() vaut false lorsque toute la table a été balayée 
// attention, pas de ; après fetch()

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

<?php
while ($donnees = $reponse->fetch()):

// on affiche les résultats contenus dans l'array $donnees
?>

    <p>
        <strong>Jeu <?= $donnees['ID']; ?> :</strong> <?= $donnees['nom']; ?><br />

        Le possesseur de ce jeu est <?= $donnees['possesseur']; ?>, il le vend à <?= $donnees['prix']; ?> euros !<br />

        Ce jeu fonctionne sur <?= $donnees['console']; ?> et on peut y jouer à <?= $donnees['nbre_joueurs_max']; ?> au maximum<br />

        <?= $donnees['possesseur']; ?> a laissé ces commentaires sur <?= $donnees['nom']; ?> : <em><?= $donnees['commentaires']; ?> </em>
    </p>

<?php 
endwhile;
?>
</body>
</html>

<?php 
/*
-----------------------------------------------------------------------
4) Fermeture de la connexion à MySQL
-----------------------------------------------------------------------
*/

// termine le traitement de la requête sql
// closeCursor() : méthode de la classe PDOStatement
// elle ferme le curseur d'analyse des résultats
// permettant à la requête d'être de nouveau exécutée
$reponse ->closeCursor();

?>
