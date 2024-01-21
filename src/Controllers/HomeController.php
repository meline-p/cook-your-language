<?php

namespace App\controllers;

/**
 * Class representing the homepage
 *
 * This class manages homepage
 */
class HomeController
{
    /**
     * Constructor for initializing the Controller with UserRepository and PostRepository dependencies.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Display the home page with all posts.
     *
     * @return void
     */
    public function home()
    {
        $data = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recipiesController = new RecipiesController();
            $data = $recipiesController->postRecipies();
        }

        require_once(__DIR__ . '/../../templates/homepage.php');
    }
}
