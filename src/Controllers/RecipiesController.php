<?php

namespace App\controllers;

use App\lib\DatabaseConnection;
use App\Model\Entity\Action;
use App\Model\Entity\Country;
use App\Model\Entity\Ingredient;
use App\Model\Entity\StepIngredient;
use App\Model\Entity\Quantity;
use App\Model\Entity\Recipe;
use App\Model\Entity\RecipeCategory;
use App\Model\Entity\RecipeDescription;
use App\Model\Entity\RecipeIngredient;
use App\Model\Entity\RecipeName;
use App\Model\Entity\RecipeTotalTime;
use App\Model\Entity\Season;
use App\Model\Entity\StepAction;
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

    public function updateEntityField($entity, $lang, $newFieldValue)
    {
        // Vérifie si la langue spécifiée est valide
        if (in_array($lang, ['en', 'fr', 'es'])) {
            // Utilise la propriété correspondante pour mettre à jour la colonne dans la bonne langue
            $entity->{$lang} = $newFieldValue;
        } else {
            // Gère le cas où la langue spécifiée n'est pas valide
            echo "Langue non prise en charge : $lang";
        }

    }

    public function getAddRecipe()
    {
        require(__DIR__.'/../../templates/recipes_list_page.php');
    }

    public function postAddRecipe($data)
    {
        $lang = $data['recipe']['recipe_lang'];

        $recipe_name = new RecipeName();
        $this->updateEntityField($recipe_name, $lang, $data['recipe']['name']);
        $recipeNameId = $this->recipeRepository->insertData('recipes_names', $lang, $data['recipe']['name']);
        if ($recipeNameId === null) {
            return;
        }

        $recipe_description = new RecipeDescription();
        $this->updateEntityField($recipe_description, $lang, $data['recipe']['description']);
        $recipeDescriptionId = $this->recipeRepository->insertData('recipies_descriptions', $lang, $data['recipe']['description']);
        if ($recipeDescriptionId === null) {
            return;
        }

        $recipe_category = new RecipeCategory();
        $this->updateEntityField($recipe_category, $lang, $data['recipe']['category']);
        $recipeCategoryId = $this->recipeRepository->insertData('recipies_categories', $lang, $data['recipe']['category']);
        if ($recipeCategoryId === null) {
            return;
        }

        $recipe_total_time = new RecipeTotalTime();
        $this->updateEntityField($recipe_total_time, $lang, $data['recipe']['total_time']);
        $recipeTotalTimeId = $this->recipeRepository->insertData('recipes_total_time', $lang, $data['recipe']['total_time']);
        if ($recipeTotalTimeId === null) {
            return;
        }

        $country = new Country();
        $this->updateEntityField($country, $lang, $data['recipe']['from']);
        $countryId = $this->recipeRepository->insertData('countries', $lang, $data['recipe']['from']);
        if ($countryId === null) {
            return;
        }

        $season = new Season();
        $this->updateEntityField($season, $lang, $data['recipe']['seasonality']);
        $seasonId = $this->recipeRepository->insertData('seasons', $lang, $data['recipe']['seasonality']);
        if ($seasonId === null) {
            return;
        }

        $recipe = new Recipe();
        $recipe->init(
            $recipeNameId,
            $recipeTotalTimeId,
            $data['recipe']['number_of_people'],
            $data['recipe']['difficulty'],
            $recipeDescriptionId,
            $recipeCategoryId,
            $countryId,
            $seasonId,
            $data['recipe']['specificities']['lactose'] ? 1 : 0,
            $data['recipe']['specificities']['gluten'] ? 1 : 0,
            $data['recipe']['specificities']['vegetarian'] ? 1 : 0,
            $data['recipe']['specificities']['vegan'] ? 1 : 0,
            $data['recipe']['specificities']['spicy'] ? 1 : 0,
            $this->currentTime
        );
        $recipe_id = $this->recipeRepository->insertRecipe(
            $recipeNameId,
            $recipeTotalTimeId,
            $data['recipe']['number_of_people'],
            $data['recipe']['difficulty'],
            $recipeDescriptionId,
            $recipeCategoryId,
            $countryId,
            $seasonId,
            $data['recipe']['specificities']['lactose'] ? 1 : 0,
            $data['recipe']['specificities']['gluten'] ? 1 : 0,
            $data['recipe']['specificities']['vegetarian'] ? 1 : 0,
            $data['recipe']['specificities']['vegan'] ? 1 : 0,
            $data['recipe']['specificities']['spicy'] ? 1 : 0,
        );
        if ($recipe_id === null) {
            return;
        }

        // INGREDIENT ET QUANTITE
        foreach ($data['recipe']['ingredients'] as $ingredientData) {
            $ingredientData['name'] = preg_replace('/\(([^)]+)\)/', '$1', $ingredientData['name']);

            $ingredient = new Ingredient();
            $this->updateEntityField($ingredient, $lang, $ingredientData['name']);
            $ingredient_id = $this->recipeRepository->insertData('ingredients', $lang, $ingredientData['name']);
            if ($ingredient_id === null) {
                return;
            }

            $quantityValue = ($ingredientData['quantity'] === null) ? 0 : $ingredientData['quantity'];
            $quantity = new Quantity();
            $this->updateEntityField($quantity, $lang, $quantityValue);
            $quantity_id = $this->recipeRepository->insertData('quantities', $lang, $quantityValue);
            if ($quantity_id === null) {
                return;
            }

            $recipe_ingredient = new RecipeIngredient();
            $recipe_ingredient->init($recipe_id, $quantity_id, $ingredient_id);
            $this->recipeRepository->insertRecipeIngredient($recipe_id, $ingredient_id, $quantity_id);
        }

        // STEPS
        foreach ($data['recipe']['steps'] as $step) {
            $step['action'] = preg_replace('/\(([^)]+)\)/', '$1', $step['action']);

            $action = new Action();
            $this->updateEntityField($action, $lang, $step['action']);
            $action_id = $this->recipeRepository->insertData('actions', $lang, $step['action']);

            $stepAction = new StepAction();
            $stepAction->init($recipe_id, $action_id);
            $step_id = $this->recipeRepository->insertStepAction($recipe_id, $action_id);
            if ($step_id === null) {
                return;
            }

            if (is_array($step['ingredient'])) {

                foreach ($step['ingredient'] as $ingredientData) {
                    $ingredientData = preg_replace('/\(([^)]+)\)/', '$1', $ingredientData);

                    $currentIngredient = new Ingredient();
                    $this->updateEntityField($currentIngredient, $lang, $ingredientData['name']);
                    $ingredient_id = $this->recipeRepository->insertData('ingredients', $lang, $ingredientData['name']);
                    if ($ingredient_id === null) {
                        return;
                    }

                    $stepIngredient = new StepIngredient();
                    $stepIngredient->init($step_id, $ingredient_id);
                    $this->recipeRepository->insertStepIngredient($step_id, $ingredient_id);
                }
            } elseif ($step['ingredient'] != null) {
                $step['ingredient'] = preg_replace('/\(([^)]+)\)/', '$1', $step['ingredient']);

                $ingredient = new Ingredient();
                $this->updateEntityField($ingredient, $lang, $step['ingredient']);
                $ingredient_id = $this->recipeRepository->insertData('ingredients', $lang, $step['ingredient']);
                if ($ingredient_id === null) {
                    return;
                }

                $stepIngredient = new StepIngredient();
                $stepIngredient->init($step_id, $ingredient_id);
                $this->recipeRepository->insertStepIngredient($step_id, $ingredient_id);
            }
        }

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
