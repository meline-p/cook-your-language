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
    public DatabaseConnection $connection;
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


    // INSERER UNE RECETTE

    public function insertData($table, $recipe_lang, $data)
    {
        $insertData = $this->connection->getConnection()->prepare("INSERT INTO $table($recipe_lang) VALUES (:data)");
        $insertData->execute(['data' => $data]);

        if ($insertData->rowCount() > 0) {
            return $this->connection->getConnection()->lastInsertId();
        } else {
            return null;
        }
    }

    public function insertRecipe(
        $name_id,
        $time_id,
        $number_of_people,
        $level_id,
        $description_id,
        $category_id,
        $country_id,
        $season_id,
        $lactose,
        $gluten,
        $vegetarian,
        $vegan,
        $spicy,
    ) {
        $insertRecipe = $this->connection->getConnection()->prepare(
            'INSERT INTO recipes(
                name_id, 
                time_id, 
                number_of_people,
                level_id,
                description_id,
                category_id,
                country_id,
                season_id,
                lactose,
                gluten,
                vegetarian,
                vegan,
                spicy,
            ) 
            VALUES (
                :name_id, 
                :time_id, 
                :number_of_people,
                :level_id,
                :description_id,
                :category_id,
                :country_id,
                :season_id,
                :lactose,
                :gluten,
                :vegetarian,
                :vegan,
                :spicy,
            )'
        );
        $insertRecipe->execute([
            'name_id' => $name_id,
            'time_id' => $time_id,
            'number_of_people' => $number_of_people,
            'level_id' => $level_id,
            'description_id' => $description_id,
            'category_id' => $category_id,
            'country_id' => $category_id,
            'season_id' => $season_id,
            'lactose' => $lactose,
            'gluten' => $gluten,
            'vegetarian' => $vegetarian,
            'vegan' => $vegan,
            'spicy' => $spicy,
        ]);

        if ($insertRecipe->rowCount() > 0) {
            return $this->connection->getConnection()->lastInsertId();
        } else {
            return null;
        }
    }

    public function insertRecipeIngredient($recipe_id, $ingredient_id, $quantity_id)
    {
        $insertIngredientsQuantities = $this->connection->getConnection()->prepare('INSERT INTO recipes_ingredients(recipe_id, ingredient_id, quantity_id) VALUES (:recipe_id, :ingredient_id, :quantity_id)');
        $insertIngredientsQuantities->execute([
            'recipe_id' => $recipe_id,
            'ingredient_id' => $ingredient_id,
            'quantity_id' => $quantity_id
        ]);
    }

    public function insertStepAction($recipe_id, $action_id)
    {
        $insertSteps = $this->connection->getConnection()->prepare('INSERT INTO steps_actions(recipe_id, action_id) VALUES (:recipe_id, :action_id)');
        $insertSteps->execute([
            'recipe_id' => $recipe_id,
            'action_id' => $action_id,
        ]);
        if ($insertSteps->rowCount() > 0) {
            return $this->connection->getConnection()->lastInsertId();
        } else {
            return null;
        }
    }

    public function insertStepIngredient($step_id, $ingredient_id)
    {
        $insertIngredientsSteps = $this->connection->getConnection()->prepare('INSERT INTO steps_ingredients_(step_id, ingredient_id) VALUES (:step_id, :ingredient_id)');
        $insertIngredientsSteps->execute([
            'step_id' => $step_id,
            'ingredient_id' => $ingredient_id,
        ]);
    }
}
