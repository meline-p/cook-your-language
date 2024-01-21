<?php

namespace App\controllers;

class RecipiesController
{
    public function getRecipies()
    {
        // Remplacez 'YOUR_API_KEY' par votre clé d'API MealDB
        $apiKey = '1';

        // ID de recette à récupérer
        $recipeId = 'random';

        // URL de l'API MealDB
        $apiUrl = "https://www.themealdb.com/api/json/v2/{$apiKey}/search.php?s=chicken";

        // Effectuez la requête HTTP
        $response = file_get_contents($apiUrl);

        $data = json_decode($response, true);

        require_once(__DIR__ . '/../../templates/recipies_list_page.php');
    }

    public function postRecipies()
    {


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer la valeur du champ "ingredient"
            $ingredient = isset($_POST['ingredient']) ? $_POST['ingredient'] : null;

            $apiKey = '1';

            // URL de l'API MealDB
            $apiUrl = "https://www.themealdb.com/api/json/v2/{$apiKey}/search.php?s={$ingredient}";

            $response = file_get_contents($apiUrl);

            $data = json_decode($response, true);
            return $data;
        }


    }
}
