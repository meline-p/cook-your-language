<?php

namespace App\controllers;

use App\lib\DatabaseConnection;
use App\Model\Repository\RecipeRepository;

class RecipiesController
{
    private $recipeRepository;
    private $currentTime;

    public function __construct(DatabaseConnection $connection)
    {
        $this->recipeRepository = new RecipeRepository($connection);
        $this->currentTime = date('Y-m-d H:i:s');
    }

    public function getAddRecipe()
    {
        require(__DIR__.'/../../templates/recipes_list_page.php');
    }

    public function postAddRecipe($data)
    {
        // $post = new Post();
        // $post->init(
        //     $data["title"],
        //     $data["chapo"],
        //     $data["content"],
        //     intval($data["user_id"]),
        //     isset($data["is_published"]) ? 1 : 0
        // );

        // $this->postRepository->addPost($post);

        // AlertService::add('success', "La publication " . $post->title . " a bien été ajoutée.");
        // header("location: /admin/publications");
        // exit;
    }

    public function getRecipies()
    {
        // // Remplacez 'YOUR_API_KEY' par votre clé d'API MealDB
        // $apiKey = '1';

        // // ID de recette à récupérer
        // $recipeId = 'random';

        // // URL de l'API MealDB
        // $apiUrl = "https://www.themealdb.com/api/json/v2/{$apiKey}/search.php?s=chicken";

        // // Effectuez la requête HTTP
        // $response = file_get_contents($apiUrl);

        // $data = json_decode($response, true);

        require_once(__DIR__ . '/../../templates/recipies_list_page.php');
    }

    // public function postRecipies()
    // {


    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //     // Récupérer la valeur du champ "ingredient"
    //     $ingredient = isset($_POST['ingredient']) ? $_POST['ingredient'] : null;

    //     $apiKey = '1';

    //     // URL de l'API MealDB
    //     $apiUrl = "https://www.themealdb.com/api/json/v2/{$apiKey}/search.php?s={$ingredient}";

    //     $response = file_get_contents($apiUrl);

    //     $data = json_decode($response, true);
    //     return $data;
    // }


    // }
}
