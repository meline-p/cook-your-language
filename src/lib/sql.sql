-- Drop tables if they exist

DROP TABLE IF EXISTS recipes_specs;

-- DROP TABLE IF EXISTS ratings;

DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS favorites_categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS steps_ingredients;
DROP TABLE IF EXISTS recipes_ingredients;
DROP TABLE IF EXISTS steps_actions;
DROP TABLE IF EXISTS quantities;
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS actions;

DROP TABLE IF EXISTS recipes;

DROP TABLE IF EXISTS recipes_names;
DROP TABLE IF EXISTS recipes_descriptions;
DROP TABLE IF EXISTS recipes_categories;
DROP TABLE IF EXISTS specs;
DROP TABLE IF EXISTS countries;
DROP TABLE IF EXISTS seasons;
DROP TABLE IF EXISTS utensils;
DROP TABLE IF EXISTS recipes_total_time;

-- Create tables
CREATE TABLE Recipes_Names (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE recipes_descriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE recipes_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE specs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE countries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    flag VARCHAR(255) UNIQUE,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE seasons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE utensils (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE Actions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE Ingredients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE Quantities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE Recipes_Total_Time (
    id INT PRIMARY KEY AUTO_INCREMENT,
    en VARCHAR(255) UNIQUE,
    fr VARCHAR(255) UNIQUE,
    es VARCHAR(255) UNIQUE
);

CREATE TABLE Recipes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name_id INT,
    time_id INT,
    number_of_people INT,
    level_id INT,
    description_id INT,
    category_id INT,
    country_id INT,
    season_id INT,
    lactose BOOLEAN,
    gluten BOOLEAN,
    vegetarian BOOLEAN,
    vegan BOOLEAN,
    spicy BOOLEAN,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    FOREIGN KEY (name_id) REFERENCES Recipes_Names(id),
    FOREIGN KEY (time_id) REFERENCES Recipes_Total_Time(id),
    FOREIGN KEY (description_id) REFERENCES recipes_descriptions(id),
    FOREIGN KEY (category_id) REFERENCES recipes_categories(id),
    FOREIGN KEY (country_id) REFERENCES countries(id),
    FOREIGN KEY (season_id) REFERENCES seasons(id)
);

CREATE TABLE Steps_Actions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipe_id INT,
    action_id INT,
    FOREIGN KEY (recipe_id) REFERENCES Recipes(id),
    FOREIGN KEY (action_id) REFERENCES Actions(id)
);

CREATE TABLE Steps_Ingredients (
    step_id INT,
    ingredient_id INT,
    utensil_id INT,
    PRIMARY KEY (step_id, ingredient_id),
    FOREIGN KEY (step_id) REFERENCES Steps_Actions(id),
    FOREIGN KEY (ingredient_id) REFERENCES Ingredients(id),
    FOREIGN KEY (utensil_id) REFERENCES utensils(id)
);

CREATE TABLE Recipes_Ingredients (
    recipe_id INT,
    ingredient_id INT,
    quantity_id INT,
    PRIMARY KEY (recipe_id, ingredient_id, quantity_id),
    FOREIGN KEY (recipe_id) REFERENCES Recipes(id),
    FOREIGN KEY (ingredient_id) REFERENCES Ingredients(id),
    FOREIGN KEY (quantity_id) REFERENCES Quantities(id)
);
CREATE TABLE recipes_specs (
    recipe_id INT,
    spec_id INT,
    PRIMARY KEY (recipe_id, spec_id),
    FOREIGN KEY (recipe_id) REFERENCES Recipes(id),
    FOREIGN KEY (spec_id) REFERENCES specs(id)
);


CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- CREATE TABLE ratings (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     recipe_id INT,
--     user_id INT,
--     rating INT CHECK(rating >= 1 AND rating <= 5) DEFAULT NULL,
--     date_rating DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (recipe_id) REFERENCES Recipes(id),
--     FOREIGN KEY (user_id) REFERENCES users(id)
-- );

CREATE TABLE favorites_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    favorite_category_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    recipe_id INT,
    favorite_category_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (recipe_id) REFERENCES Recipes(id),
    FOREIGN KEY (favorite_category_id) REFERENCES favorites_categories(id)
);
