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
use App\Model\Entity\Utensil;
use App\Model\Repository\RecipeRepository;

class RecipesController
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
        $recipesNames = $this->recipeRepository->getRecipiesName();
        require_once(__DIR__.'/../../templates/add_recipe.php');
    }

    public function postAddRecipe($data)
    {

        $RawData = $_POST['data-chatgpt'];

        $data = json_decode($RawData, true);

        $lang = $data['recipe']['recipe_lang'];

        // require_once(__DIR__.'/../../templates/post_add_recipe.php');

        $recipe_name = new RecipeName();
        $this->updateEntityField($recipe_name, $lang, $data['recipe']['name']);
        $recipeNameId = $this->recipeRepository->exist('recipes_names', $lang, $data['recipe']['name']);
        if ($recipeNameId === null) {
            return;
        }

        $recipe_description = new RecipeDescription();
        $this->updateEntityField($recipe_description, $lang, $data['recipe']['description']);
        $recipeDescriptionId = $this->recipeRepository->exist('recipes_descriptions', $lang, $data['recipe']['description']);
        if ($recipeDescriptionId === null) {
            return;
        }

        $recipe_category = new RecipeCategory();
        $this->updateEntityField($recipe_category, $lang, $data['recipe']['category']);
        $recipeCategoryId = $this->recipeRepository->exist('recipes_categories', $lang, $data['recipe']['category']);
        if ($recipeCategoryId === null) {
            return;
        }

        $recipe_total_time = new RecipeTotalTime();
        $this->updateEntityField($recipe_total_time, $lang, $data['recipe']['total_time']);
        $recipeTotalTimeId = $this->recipeRepository->exist('recipes_total_time', $lang, $data['recipe']['total_time']);
        if ($recipeTotalTimeId === null) {
            return;
        }

        $country = new Country();
        $this->updateEntityField($country, $lang, $data['recipe']['from']);
        $countryId = $this->recipeRepository->exist('countries', $lang, $data['recipe']['from']);
        if ($countryId === null) {
            return;
        }

        $season = new Season();
        $this->updateEntityField($season, $lang, $data['recipe']['seasonality']);
        $seasonId = $this->recipeRepository->exist('seasons', $lang, $data['recipe']['seasonality']);
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

        // USTENSILS

        try {
            foreach ($data['recipe']['utensils'] as $utensilData) {
                $utensilData = preg_replace('/\(([^)]+)\)/', '$1', $utensilData);

                $utensil = new Utensil();
                $this->updateEntityField($utensil, $lang, $utensilData);
                $utensil_id = $this->recipeRepository->exist('utensils', $lang, $utensilData);
                if($utensil_id === null) {
                    return;
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, déclencher une exception avec un message descriptif
            throw new \Exception("Erreur de traitement de la recette : Ustensils : " . $e->getMessage());
        }


        // INGREDIENT ET QUANTITE
        try {
            foreach ($data['recipe']['ingredients'] as $ingredientData) {
                $ingredientData['name'] = preg_replace('/\(([^)]+)\)/', '$1', $ingredientData['name']);

                $ingredient = new Ingredient();
                $this->updateEntityField($ingredient, $lang, $ingredientData['name']);
                $ingredient_id = $this->recipeRepository->exist('ingredients', $lang, $ingredientData['name']);
                if ($ingredient_id === null) {
                    return;
                }

                $quantityValue = ($ingredientData['quantity'] === null) ? 0 : $ingredientData['quantity'];
                $quantity = new Quantity();
                $this->updateEntityField($quantity, $lang, $quantityValue);
                $quantity_id = $this->recipeRepository->exist('quantities', $lang, $quantityValue);
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
                $action_id = $this->recipeRepository->exist('actions', $lang, $step['action']);

                $stepAction = new StepAction();
                $stepAction->init($recipe_id, $action_id);
                $step_id = $this->recipeRepository->insertStepAction($recipe_id, $action_id);
                if ($step_id === null) {
                    echo 'erreur action';
                    return;
                }

                if (is_array($step['ingredient'])) {

                    foreach ($step['ingredient'] as $ingredientData) {
                        $ingredientData = preg_replace('/\(([^)]+)\)/', '$1', $ingredientData);

                        $currentIngredient = new Ingredient();
                        $this->updateEntityField($currentIngredient, $lang, $ingredientData);
                        $ingredient_id = $this->recipeRepository->exist('ingredients', $lang, $ingredientData);
                        if ($ingredient_id === null) {
                            echo 'erreur step ingredient';
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
                    $ingredient_id = $this->recipeRepository->exist('ingredients', $lang, $step['ingredient']);
                    if ($ingredient_id === null) {
                        echo 'erreur step ingredient';
                        return;
                    }

                    $stepIngredient = new StepIngredient();
                    $stepIngredient->init($step_id, $ingredient_id);
                    $this->recipeRepository->insertStepIngredient($step_id, $ingredient_id);
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, déclencher une exception avec un message descriptif
            throw new \Exception("Erreur de traitement de la recette : " . $e->getMessage());
        }

                // AlertService::add('success', "La publication " . $post->title . " a bien été ajoutée.");
                // header("location: /admin/publications");
                // exit;
    }

    public function getRecipes()
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

        require_once(__DIR__ . '/../../templates/recipes_list_page.php');
    }

    // public function postRecipes()
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
