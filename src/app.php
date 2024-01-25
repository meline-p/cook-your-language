<?php

use App\lib\DatabaseConnection;
use App\controllers\HomeController;
use App\controllers\RecipiesController;

session_start();

$databaseConnection = new DatabaseConnection();

// Création des instances de UserRepository et PostRepository

$uri = $_SERVER['REQUEST_URI'];

// ----------------- HOMEPAGE ----------------
if ($uri === '/') {
    $home = new HomeController($databaseConnection);
    $home->home();

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $recipies = new RecipiesController();
//     $recipies->postRecipies();
// }

} elseif ($uri === '/recipies') {
    $recipies = new RecipiesController($databaseConnection);
    $recipies->getRecipies();
}
