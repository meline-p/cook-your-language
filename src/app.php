<?php

use App\lib\DatabaseConnection;
use App\controllers\HomeController;
use App\controllers\RecipesController;

session_start();

$databaseConnection = new DatabaseConnection();

// Création des instances de UserRepository et PostRepository

$uri = $_SERVER['REQUEST_URI'];

// ----------------- HOMEPAGE ----------------
if ($uri === '/') {
    $home = new HomeController($databaseConnection);
    $home->home();

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $recipes = new RecipesController();
//     $recipes->postRecipes();
// }

} elseif ($uri === '/recipes') {
    $recipes = new RecipesController($databaseConnection);
    $recipes->getRecipes();

} elseif ($uri === '/add_recipe') {
    $recipes = new RecipesController($databaseConnection);
    $recipes->getAddRecipe();
} elseif ($uri === '/post_add_recipe') {
    $recipes = new RecipesController($databaseConnection);
    $recipes->postAddRecipe($_POST);
}
