<?php

namespace App\Model\Repository;

use App\lib\DatabaseConnection;
use App\Model\Entity\RecipeName;
use App\Model\Entity\RecipeTotalTime;
use App\Model\Entity\Recipe;
use App\Model\Entity\Ingredient;
use App\Model\Entity\Quantity;
use App\Model\Entity\RecipeIngredient;
use App\Model\Entity\Action;
use App\Model\Entity\Step;
use ReturnTypeWillChange;

/**
 * Class representing Post repository.
 */
class RecipeRepository
{
    private DatabaseConnection $connection;
    public $current_time;

    /**
     * Constructor for the PostRepository class.
     *
     * @param  DatabaseConnection $connection The database connection object.
     * @return void
     */
    public function __construct(DatabaseConnection $connection)
    {
        $this->connection = $connection;
        $this->current_time = new \DateTime();
    }

    public function getAllRecipes()
    {
        $statement = $this->connection->getConnection()->prepare(
            "SELECT 
            rn.fr AS recipe_name, 
            rtt.fr AS recipe_total_time, 
            r.number_of_people AS number_of_people,
            i.fr AS ingredient_name,
            q.fr AS quantity,
            a.fr AS step_action
            from recipes r
            INNER JOIN recipes_names rn ON rn.id = r.name_id
            INNER JOIN recipes_total_time rtt ON rtt.id = r.time_id
            INNER JOIN recipes_ingredients ri ON ri.recipe_id = r.id
            INNER JOIN ingredients i ON i.id = ri.ingredient_id 
            INNER JOIN quantities q ON q.id = ri.quantity_id
            INNER JOIN steps s ON s.recipe_id = r.id
            INNER JOIN actions a ON a.id = s.action_id
            INNER JOIN ingredients_steps ist ON ist.step_id = s.id
            "
        );
        $statement->execute();

        $rows =  $statement->fetchAll();
        $recipes = array_map(function ($row) {
            $recipe = new Recipe();
            $recipe->fromSql($row);
            return $recipe;
        }, $rows);

        return $recipes;
    }

    public function countAllRecipes()
    {
        $statement = $this->connection->getConnection()->prepare('SELECT COUNT(1) AS nb FROM recipes');
        $statement->execute();

        $row = $statement->fetch();

        return $row['nb'];
    }



    private function createRecipe($data)
    {
        // insertion du nom et du temps total de la recette
        $recipeNameId = $this->insertData('recipes_names', 'fr', $data['recipe']['name']);
        $recipeTotalTimeId = $this->insertData('recipes_total_time', 'fr', $data['recipe']['total_time']);

        // insertion des ID + nombre de personnes dans recipes
        $insertRecipe = $this->connection->getConnection()->prepare('INSERT IGNORE INTO recipes(name_id, time_id, number_of_people) VALUES (:name_id, :time_id, :number_of_people)');
        $insertRecipe->execute([
            'name_id' => $recipeNameId,
            'time_id' => $recipeTotalTimeId,
            'number_of_people' => $data['recipe']['number_of_people']
        ]);

        // récupérer l'ID de la recette créée
        return $this->connection->getConnection()->lastInsertId();
    }

    private function insertData($table, $columnName, $data)
    {
        $insertData = $this->connection->getConnection()->prepare("INSERT IGNORE INTO $table($columnName) VALUES (:data)");
        $insertData->execute(['data' => $data]);
        return $this->connection->getConnection()->lastInsertId();
    }

    private function getDataById($table, $columns, $conditions, $params)
    {
        $query = "SELECT $columns FROM $table WHERE $conditions";
        $statement = $this->connection->getConnection()->prepare($query);
        $statement->execute($params);
        return $statement->fetch();
    }

    private function addIngredient($ingredient)
    {
        // l'ingrédient existe dans la BDD ?
        $existingIngredient = $this->getDataById('ingredients', 'id', 'fr = :fr', ['fr' => $ingredient]);
        if ($existingIngredient === false) {
            $ingredientId = $this->insertData('ingredients', 'fr', $ingredient);
        } else {
            $ingredientId = $existingIngredient['id'];
        }

        return $ingredientId;
    }

    private function addIngredientsWithQuantities($ingredientsData, $recipeId)
    {
        foreach ($ingredientsData as $ingredient) {
            $ingredient['name'] = preg_replace('/\(([^)]+)\)/', '$1', $ingredient['name']);

            // ID de l'ingrédient
            $ingredientId = $this->addIngredient($ingredient['name']);

            // si la quantité est null
            if($ingredient['quantity'] == null) {
                $ingredient['quantity'] = 0;
            }

            // la quantité existe dans la BDD ?
            $existingQuantity = $this->getDataById('quantities', 'id', 'fr = :fr', ['fr' => $ingredient['quantity']]);
            if ($existingQuantity === false) {
                $quantityId = $this->insertData('quantities', 'fr', $ingredient['quantity']);
            } else {
                $quantityId = $existingQuantity['id'];
            }

            // Insérer les ID dans la table recipes_ingredients
            $insertIngredientsQuantities = $this->connection->getConnection()->prepare('INSERT INTO recipes_ingredients(recipe_id, ingredient_id, quantity_id) VALUES (:recipe_id, :ingredient_id, :quantity_id)');
            $insertIngredientsQuantities->execute([
                'recipe_id' => $recipeId,
                'ingredient_id' => $ingredientId,
                'quantity_id' => $quantityId
            ]);

        }
    }

    private function addIngredientStep($ingredientData, $stepId)
    {
        if (is_array($ingredientData)) {

            // Pour chaque ingrédient
            foreach ($ingredientData as $ingredient) {

                $ingredient = preg_replace('/\(([^)]+)\)/', '$1', $ingredient);

                // ID de l'ingrédient
                $ingredientId = $this->addIngredient($ingredient['name']);

                // Associer l'ingrédient à l'étape
                $insertIngredientsSteps = $this->connection->getConnection()->prepare('INSERT INTO ingredients_steps(step_id, ingredient_id) VALUES (:step_id, :ingredient_id)');
                $insertIngredientsSteps->execute([
                    'step_id' => $stepId,
                    'ingredient_id' => $ingredientId,
                ]);
            }
        } elseif ($ingredientData != null) {
            // Pour une seule ingrédient

            $ingredient = preg_replace('/\(([^)]+)\)/', '$1', $ingredientData);
            // ID de l'ingrédient
            $ingredientId = $this->addIngredient($ingredient['name']);

            // Associer l'ingrédient à l'étape
            $insertIngredientsSteps = $this->connection->getConnection()->prepare('INSERT INTO ingredients_steps(step_id, ingredient_id) VALUES (:step_id, :ingredient_id)');
            $insertIngredientsSteps->execute([
                'step_id' => $stepId,
                'ingredient_id' => $ingredientId,
            ]);
        }
    }

    private function addSteps($stepsData, $recipeId)
    {
        foreach ($stepsData as $step) {
            $step['action'] = preg_replace('/\(([^)]+)\)/', '$1', $step['action']);

            // l'action existe dans la BDD ?
            $existingAction = $this->getDataById('actions', 'id', 'fr = :fr', $step['action']);
            if ($existingAction === false) {
                $actionId = $this->insertData('actions', 'fr', $step['action']);
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
            $this->addIngredientStep($step['ingredient'], $stepId);

        }
    }

    public function addRecipe($data)
    {
        $recipeId = $this->createRecipe($data);

        $this->addIngredientsWithQuantities($data['recipe']['ingredients'], $recipeId);

        $this->addSteps($data['recipe']['steps'], $recipeId);
    }
}
