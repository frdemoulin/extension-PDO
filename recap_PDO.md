# L'extension PDO

PDO (PHP Data Objects) est une extension (= API) définissant l'interface pour accéder à plusieurs types de base de données, fournie automatiquement depuis PHP 5.1. PDO n'a pas pour but que d'interpréter les requêtes et de les traduire pour tous les SGBD. C'est pour cette raison qu'il est nécessaire d'indiquer à PDO le type de SGBD à utiliser.

## Les requêtes préparées

### Le principe

Ce concept n'est pas propre à PDO et ce n'est que l'une de ses possibilités. Le principe d'une requête préparée est de soumettre un moule de requête et de placer des place holders aux endroits où l'on voudra insérer des valeurs dynamiques (pas de noms de tables, de champs ni de commandes). Un place holder représente ainsi une seule et unique valeur. Concrètement, on retiendra qu'_une requête préparée sépare les données de la structure de la requête_.

__Exemple :__ on symbolise les places holders par des `?` appelés _marqueurs_

```
SELECT * FROM foo
WHERE id=? AND bar<?
LIMIT ?;
```

Le SGBD va préparer la requête, c'est-à-dire interpréter, compiler et stocker temporairement son "plan d'exécution logique". Il faut ensuite associer des valeurs aux place holders, qui agissent un peu comme des variables.

```
place holder #1 = 1
place holder #2 = 100
place holder #3 = 3
```

Ensuite, ces valeurs seront soumises en demandant la compilation et l'exécution de la requête qui a été préparée. Le SGDB saura alors que ce qu'elle insère (des valeurs et non des commandes). L'assemblage de la requête se fera en interne du SGBD, ce qui explique que l'on ne peut pas réellement déboguer la valeur compilée de la requête dans le code PHP. Dans l'exemple présent, la requête exécutée sera donc :

```
SELECT *
FROM foo
WHERE id=1 AND bar<100
LIMIT 3;
```

### L'intérêt

A priori, les requêtes préparées présentent des _inconvénients_ :

* il faut écrire plus de lignes de code que pour une requête simple ;

* pour une exécution unique, les performances sont moindres qu'avec une exécution directe ;

* le débogage d'une requête est légèrement plus complexe ;

* concrètement, le résultat est, à toutes fins pratiques, identique à l'exécution d'une requête.

Ce concept a été introduit exactement pour la même raison que l'on a inventé les systèmes de templates : pour isoler les données du traitement.

L'utilisation de requêtes préparées offre les _avantages_ suivants :

* impose une certaine rigueur de programmation ;

* optimisation du temps d'exécution requis pour les requêtes exécutées plus d'une fois ;

* plus grande sécurité au niveau des requêtes (prévenir les injections SQL requiert de séparer les données non sûres des commandes et requêtes, c'est justement ce que font les requêtes préparées).

Et ça tombe bien ; c'est très précisément ce que font les requêtes préparées : séparer les données de la structure de la requête !).

## PDO en pratique

### Les méthodes à retenir

* `query()` : retourne un jeu de résultats sous la forme d'un objet PDOStatement ;

* `exec() ` : retourne uniquement le nombre de lignes affectées ;

* `fetch()` : récupère la ligne suivante d'un jeu de résultats PDO ;

* `fetchAll()` : retourne un tableau contenant toutes les lignes du jeu d'enregistrements ;

* `prepare()` : prépare une requête à l'exécution et retourne un objet ;

* `execute()` : exécute une requête préparée.

### Se connecter avec PDO

On commence par établir avec PDO une connexion à une base de données :

```
try {
    $strConnection = 'mysql:host=localhost;dbname=ma_base'; //Ligne 1
    $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); //Ligne 2
    $pdo = new PDO($connStr, 'Utilisateur', 'Mot de passe', $arrExtraParam); //Ligne 3; Instancie la connexion
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Ligne 4
}
catch(PDOException $e) {
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}
```

### Sans les requêtes préparées

On utilise dans ce cas les méthodes `query()` et `exec()` :

* `query()` pour des requêtes de sélection (SELECT) ;

* `exec()` pour des requêtes d'insertion (INSERT), de modification (UPDATE) ou de suppression (DELETE).

__Exemple__ - Effectuer une query et un fetch

```
$query = 'SELECT * FROM foo WHERE bar=1;';
$arr = $pdo->query($query)->fetch(); //Sur une même ligne ...
```

__Exemple__ - Effectuer une query et un fetchAll

```
$query = 'SELECT * FROM foo WHERE bar<10;';
$stmt = $pdo->query($query);
$arrAll = $stmt->fetchAll(); //... ou sur 2 lignes
```

__Exemple__ - Effectuer un exec

```
$query = 'DELETE FROM foo WHERE bar<10;';
$rowCount = $pdo->exec($query);
```

### Avec les requêtes préparées

__Exemple__ - Effectuer un prepare et un fetchAll

```
// Préparer la requête
$query = 'SELECT *'
	. ' FROM foo'
	. ' WHERE id=?'
		. ' AND cat=?'
	. ' LIMIT ?;';
$prep = $pdo->prepare($query);
 
// Associer des valeurs aux place holders
$prep->bindValue(1, 120, PDO::PARAM_INT);
$prep->bindValue(2, 'bar', PDO::PARAM_STR);
$prep->bindValue(3, 10, PDO::PARAM_INT);
 
// Compiler et exécuter la requête
$prep->execute();
 
// Récupérer toutes les données retournées
$arrAll = $prep->fetchAll();
 
// Clore la requête préparée
$prep->closeCursor();
$prep = NULL;
```

### Réutiliser une requête préparée

Outre le fait que vos paramètres sont bien protégés, l'avantage initial des requêtes préparées est la réutilisation du moule de la requête. En effet, le SGBD à déjà effectué une partie du traitement sur la requête. Il est donc possible de ré-exécuter la requête avec de nouvelles valeurs, sans pour autant devoir reprendre le traitement du départ; le découpage et l'interprétation ayant déjà été faits.

```
$query = 'INSERT INTO foo (nom, prix) VALUES (?, ?);';
$prep = $pdo->prepare($query);
 
$prep->bindValue(1, 'item 1', PDO::PARAM_STR);
$prep->bindValue(2, 12.99, PDO::PARAM_FLOAT);
$prep->execute();
 
$prep->bindValue(1, 'item 2', PDO::PARAM_STR);
$prep->bindValue(2, 7.99, PDO::PARAM_FLOAT);
$prep->execute();
 
$prep->bindValue(1, 'item 3', PDO::PARAM_STR);
$prep->bindValue(2, 17.94, PDO::PARAM_FLOAT);
$prep->execute();
 
$prep = NULL;
```

## Les classes PDO

L'extension PDO comporte _trois classes_ :

* La classe __PDO__ correspond à une connexion à la base de données.

* La classe __PDOStatement__ représente une requête préparée et le jeu de résultats de la requête une fois qu'elle est exécutée. Cette classe offre des méthodes de parcours, de comptage, d'informations.

* La classe __PDOException__ représente une erreur émise par PDO.

### Classe PDO

[Lien vers la doc PHP de la classe PDO](http://php.net/manual/fr/class.pdo.php)

__Liste des méthodes :__

* `PDO::beginTransaction` — Démarre une transaction

* `PDO::commit` — Valide une transaction

* `PDO::__construct` — Crée une instance PDO qui représente une connexion à la base

* `PDO::errorCode` — Retourne le SQLSTATE associé avec la dernière opération sur la base de données

* `PDO::errorInfo` — Retourne les informations associées à l'erreur lors de la dernière opération sur la base de données

* `PDO::exec` — Exécute une requête SQL et retourne le nombre de lignes affectées

* `PDO::getAttribute` — Récupère un attribut d'une connexion à une base de données

* `PDO::getAvailableDrivers` — Retourne la liste des pilotes PDO disponibles

* `PDO::inTransaction` — Vérifie si nous sommes dans une transaction

* `PDO::lastInsertId` — Retourne l'identifiant de la dernière ligne insérée ou la valeur d'une séquence

* `PDO::prepare` — Prépare une requête à l'exécution et retourne un objet

* `PDO::query` — Exécute une requête SQL, retourne un jeu de résultats en tant qu'objet PDOStatement

* `PDO::quote` — Protège une chaîne pour l'utiliser dans une requête SQL PDO

* `PDO::rollBack` — Annule une transaction

* `PDO::setAttribute` — Configure un attribut PDO

### Classe PDOStatement

[Lien vers la doc PHP de la classe PDOStatement]( http://php.net/manual/fr/class.pdostatement.php)

__Liste des méthodes :__

* `PDOStatement::bindColumn` — Lie une colonne à une variable PHP

* `PDOStatement::bindParam` — Lie un paramètre à un nom de variable spécifique

* `PDOStatement::bindValue` — Associe une valeur à un paramètre

* `PDOStatement::closeCursor` — Ferme le curseur, permettant à la requête d'être de nouveau exécutée

* `PDOStatement::columnCount` — Retourne le nombre de colonnes dans le jeu de résultats

* `PDOStatement::debugDumpParams` — Détaille une commande préparée SQL

* `PDOStatement::errorCode` — Récupère les informations sur l'erreur associée lors de la dernière opération sur la requête

* `PDOStatement::errorInfo` — Récupère les informations sur l'erreur associée lors de la dernière opération sur la requête

* `PDOStatement::execute` — Exécute une requête préparée

* `PDOStatement::fetch` — Récupère la ligne suivante d'un jeu de résultats PDO

* `PDOStatement::fetchAll` — Retourne un tableau contenant toutes les lignes du jeu d'enregistrements

* `PDOStatement::fetchColumn` — Retourne une colonne depuis la ligne suivante d'un jeu de résultats

* `PDOStatement::fetchObject` — Récupère la prochaine ligne et la retourne en tant qu'objet

* `PDOStatement::getAttribute` — Récupère un attribut de requête

* `PDOStatement::getColumnMeta` — Retourne les métadonnées pour une colonne d'un jeu de résultats

* `PDOStatement::nextRowset` — Avance à la prochaine ligne de résultats d'un gestionnaire de lignes de résultats multiples

* `PDOStatement::rowCount` — Retourne le nombre de lignes affectées par le dernier appel à la fonction `PDOStatement::execute()`

* `PDOStatement::setAttribute` — Définit un attribut de requête

* `PDOStatement::setFetchMode` — Définit le mode de récupération par défaut pour cette requête

### Classe PDOException

[Lien vers la doc PHP de la classe PDOException](http://php.net/manual/fr/class.pdoexception.php)

[Lien vers la doc sur la classe Exception]( http://php.net/manual/fr/class.exception.php)

__Liste des méthodes :__

* `Exception::__construct` — Construit l'exception

* `Exception::getMessage` — Récupère le message de l'exception

* `Exception::getPrevious` — Retourne l'exception précédente

* `Exception::getCode` — Récupère le code de l'exception

* `Exception::getFile` — Récupère le fichier dans lequel l'exception a été créée

* `Exception::getLine` — Récupère la ligne dans laquelle l'exception a été créée

* `Exception::getTrace` — Récupère la trace de la pile

* `Exception::getTraceAsString` — Récupère la trace de la pile en tant que chaîne

* `Exception::__toString` — Représente l'exception sous la forme d'une chaîne

* `Exception::__clone` — Clone l'exception

## Utiliser PDO en pratique

Voir fichiers php ci-joints