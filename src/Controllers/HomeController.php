<?php

namespace App\controllers;

use App\lib\DatabaseConnection;

/**
 * Class representing the homepage
 *
 * This class manages homepage
 */
class HomeController
{

    private DatabaseConnection $connection;

    /**
     * Constructor for initializing the Controller with UserRepository and PostRepository dependencies.
     *
     * @return void
     */
    public function __construct(DatabaseConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Display the home page with all posts.
     *
     * @return void
     */
    public function home()
    {
        $data = null;

        // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //     $recipiesController = new RecipiesController();
        //     $data = $recipiesController->postRecipies();
        // }


        $data = [
            "recipe" => [
                "name" => "Œuf à la coque",
                "recipe_lang" => 'fr',
                "number_of_people" => 1,
                "total_time" => "4 minutes",
                "ingredients" => [
                    ["name" => "Œuf(s)", "quantity" => "1 à 4, selon le nombre de personnes"]
                ],
                "steps" => [
                    ["action" => "Faire bouillir", "ingredient" => null, "details" => "Portez une casserole d'eau à ébullition."],
                    ["action" => "Plonger", "ingredient" => ["Œuf(s)"], "details" => "Délicatement, plongez les œufs dans l'eau bouillante à l'aide d'une cuillère."],
                    ["action" => "Cuire", "ingredient" => null, "details" => "Laissez cuire les œufs pendant 3 à 4 minutes pour obtenir des œufs à la coque avec le jaune encore coulant."],
                    ["action" => "Retirer", "ingredient" => ["Œuf(s)"], "details" => "À l'aide d'une cuillère perforée, retirez les œufs de l'eau bouillante."],
                    ["action" => "Écaler", "ingredient" => ["Œuf(s)"], "details" => "Tapez délicatement la coquille des œufs sur une surface dure, puis écalez-les."],
                    ["action" => "Assaisonner", "ingredient" => null, "details" => "Ajoutez du sel et du poivre selon votre goût."],
                    ["action" => "Déguster", "ingredient" => null, "details" => "Servez les œufs à la coque dans des coquetiers. Trempez des mouillettes de pain dans le jaune d'œuf et dégustez !"]
                ]
            ]
            
            
            
        ];
        // Utiliser le tableau associatif résultant

        $data['recipe']['name'] = preg_replace('/\(([^)]+)\)/', '$1', $data['recipe']['name']);

        $insertRecipeName = $this->connection->getConnection()->prepare('INSERT IGNORE INTO recipes_names(fr) VALUES (:fr)');
        $insertRecipeName->execute(['fr' => $data['recipe']['name']]);
        $insertRecipeName->fetch();
        $recipeNameId = $this->connection->getConnection()->lastInsertId();

        $insertTotalTime = $this->connection->getConnection()->prepare('INSERT IGNORE INTO recipes_total_time(fr) VALUES (:fr)');
        $insertTotalTime->execute(['fr' => $data['recipe']['total_time']]);
        $insertTotalTime->fetch();
        $recipeTotalTimeId = $this->connection->getConnection()->lastInsertId();

        $insertRecipe = $this->connection->getConnection()->prepare('INSERT IGNORE INTO recipes(name_id, time_id, number_of_people) VALUES (:name_id, :time_id, :number_of_people)');
        $insertRecipe->execute([
            'name_id' => $recipeNameId,
            'time_id' => $recipeTotalTimeId,
            'number_of_people' => $data['recipe']['number_of_people']
        ]);

        $insertTotalTime->fetch();
        $recipeId = $this->connection->getConnection()->lastInsertId();


        foreach ($data['recipe']['ingredients'] as $ingredient) {

            $ingredient['name'] = preg_replace('/\(([^)]+)\)/', '$1', $ingredient['name']);

            $selectIngredient = $this->connection->getConnection()->prepare('SELECT id FROM ingredients WHERE fr = :fr');
            $selectIngredient->execute(['fr' => $ingredient['name']]);
            $existingIngredient = $selectIngredient->fetch();
        
            if ($existingIngredient === false) {
                $insertIngredient = $this->connection->getConnection()->prepare('INSERT INTO ingredients(fr) VALUES (:fr)');
                $insertIngredient->execute(['fr' => $ingredient['name']]);
                $ingredientId = $this->connection->getConnection()->lastInsertId();
            } else {
                $ingredientId = $existingIngredient['id'];
            }

            if($ingredient['quantity'] == null) $ingredient['quantity'] = 0;

            $selectQuantity =  $this->connection->getConnection()->prepare('SELECT id FROM quantities WHERE fr = :fr');
            $selectQuantity->execute(['fr' => $ingredient['quantity']]);
            $existingQuantity = $selectQuantity->fetch();
        
            if ($existingQuantity === false) {
                $insertQuantity = $this->connection->getConnection()->prepare('INSERT IGNORE INTO quantities(fr) VALUES (:fr)');
                $insertQuantity->execute(['fr' => $ingredient['quantity']]);
                $quantityId = $this->connection->getConnection()->lastInsertId();
            } else {
                $quantityId = $existingQuantity['id'];
            }
        
            $insertIngredientsQuantities = $this->connection->getConnection()->prepare('INSERT INTO recipes_ingredients(recipe_id, ingredient_id, quantity_id) VALUES (:recipe_id, :ingredient_id, :quantity_id)');
            $insertIngredientsQuantities->execute([
                'recipe_id' => $recipeId,
                'ingredient_id' => $ingredientId, 
                'quantity_id' => $quantityId
            ]);
           
        }
        
        foreach ($data['recipe']['steps'] as $step) {
            // Sélectionner l'action

            $step['action'] = preg_replace('/\(([^)]+)\)/', '$1', $step['action']);

            $selectAction = $this->connection->getConnection()->prepare('SELECT id FROM actions WHERE fr = :fr');
            $selectAction->execute(['fr' => $step['action']]);
            $existingAction = $selectAction->fetch();
        
            if ($existingAction === false) {
                // L'action n'existe pas, l'ajouter
                $insertAction = $this->connection->getConnection()->prepare('INSERT IGNORE INTO actions(fr) VALUES (:fr)');
                $insertAction->execute(['fr' => $step['action']]);
                $actionId = $this->connection->getConnection()->lastInsertId();
            } else {
                $actionId = $existingAction['id'];
            }
        
            // Insérer l'étape
            $insertSteps = $this->connection->getConnection()->prepare('INSERT INTO steps(recipe_id, action_id) VALUES (:recipe_id, :action_id)');
            $insertSteps->execute([
                'recipe_id' => $recipeId,
                'action_id' => $actionId,
            ]);
            $stepId = $this->connection->getConnection()->lastInsertId();
        
            // Traiter les ingrédients
            if (is_array($step['ingredient'])) {
                foreach ($step['ingredient'] as $ingredient) {
                    // Sélectionner l'ingrédient

                    $ingredient = preg_replace('/\(([^)]+)\)/', '$1', $ingredient);

                    $selectIngredient = $this->connection->getConnection()->prepare('SELECT id FROM ingredients WHERE fr = :fr');
                    $selectIngredient->execute(['fr' => $ingredient]);
                    $existingIngredient = $selectIngredient->fetch();
        
                    if ($existingIngredient === false) {
                        // L'ingrédient n'existe pas, l'ajouter
                        $insertIngredient = $this->connection->getConnection()->prepare('INSERT INTO ingredients(fr) VALUES (:fr)');
                        $insertIngredient->execute(['fr' => $ingredient]);
                        $ingredientId = $this->connection->getConnection()->lastInsertId();
                    } else {
                        $ingredientId = $existingIngredient['id'];
                    }
        
                    // Associer l'ingrédient à l'étape
                    $insertIngredientsSteps = $this->connection->getConnection()->prepare('INSERT INTO ingredients_steps(step_id, ingredient_id) VALUES (:step_id, :ingredient_id)');
                    $insertIngredientsSteps->execute([
                        'step_id' => $stepId,
                        'ingredient_id' => $ingredientId,
                    ]);
                }
            } else if ($step['ingredient'] != null) {
                // Pour une seule ingrédient
                // Sélectionner l'ingrédient

                $step['ingredient'] = preg_replace('/\(([^)]+)\)/', '$1', $step['ingredient']);

                $selectIngredient = $this->connection->getConnection()->prepare('SELECT id FROM ingredients WHERE fr = :fr');
                $selectIngredient->execute(['fr' => $step['ingredient']]);
                $existingIngredient = $selectIngredient->fetch();
        
                if ($existingIngredient === false) {
                    // L'ingrédient n'existe pas, l'ajouter
                    $insertIngredient = $this->connection->getConnection()->prepare('INSERT INTO ingredients(fr) VALUES (:fr)');
                    $insertIngredient->execute(['fr' => $step['ingredient']]);
                    $ingredientId = $this->connection->getConnection()->lastInsertId();
                } else {
                    $ingredientId = $existingIngredient['id'];
                }
        
                // Associer l'ingrédient à l'étape
                $insertIngredientsSteps = $this->connection->getConnection()->prepare('INSERT INTO ingredients_steps(step_id, ingredient_id) VALUES (:step_id, :ingredient_id)');
                $insertIngredientsSteps->execute([
                    'step_id' => $stepId,
                    'ingredient_id' => $ingredientId,
                ]);
            }
        }
                
        require_once(__DIR__ . '/../../templates/homepage.php');
    }
}
