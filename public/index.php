<?php

require '../vendor/autoload.php'; // On charge les différentes librairies ajouté via Composer

use Root\Application;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals(); // On récupère la requête
$app = new Application($request); //On instancie une nouvelle instance de notre application
$response = $app->process(); // On commence le traitement de la requête
$response->send(); // On retourne le résultat à l'utilisateur