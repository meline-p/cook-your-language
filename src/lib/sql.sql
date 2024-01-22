
-- Drop tables if they exist
DROP TABLE IF EXISTS Ingredients_Steps;
DROP TABLE IF EXISTS Recipes_Ingredients;
DROP TABLE IF EXISTS Steps;
DROP TABLE IF EXISTS Recipes;
DROP TABLE IF EXISTS Recipes_Total_Time;
DROP TABLE IF EXISTS Quantities;
DROP TABLE IF EXISTS Ingredients;
DROP TABLE IF EXISTS Actions;
DROP TABLE IF EXISTS Recipes_Names;

-- Create tables
CREATE TABLE Recipes_Names (
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
    FOREIGN KEY (name_id) REFERENCES Recipes_Names(id),
    FOREIGN KEY (time_id) REFERENCES Recipes_Total_Time(id)
);

CREATE TABLE Steps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipe_id INT,
    action_id INT,
    FOREIGN KEY (recipe_id) REFERENCES Recipes(id),
    FOREIGN KEY (action_id) REFERENCES Actions(id)
);

CREATE TABLE Ingredients_Steps (
    step_id INT,
    ingredient_id INT,
    PRIMARY KEY (step_id, ingredient_id),
    FOREIGN KEY (step_id) REFERENCES Steps(id),
    FOREIGN KEY (ingredient_id) REFERENCES Ingredients(id)
);

CREATE TABLE Recipes_Ingredients (
    recipe_id INT,
    ingredient_id INT,
    quantity_id INT,
    PRIMARY KEY (recipe_id,ingredient_id, quantity_id),
    FOREIGN KEY (recipe_id) REFERENCES Recipes(id),
    FOREIGN KEY (ingredient_id) REFERENCES Ingredients(id),
    FOREIGN KEY (quantity_id) REFERENCES Quantities(id)
);