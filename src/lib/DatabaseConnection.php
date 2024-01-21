<?php

namespace App\lib;

/**
 * Class representing database connection.
 *
 * This class allows to establish a database connection
 * and retrieves user data from the database
 */
class DatabaseConnection
{
    /**
     * PDO instance for database connection.
     *
     * @var \PDO|null
     */
    public ?\PDO $db = null;

    /**
     * Establishes a database connection and returns the PDO instance.
     *
     * @return \PDO The PDO instance.
     */
    public function getConnection(): \PDO
    {
        // Check if the PDO instance is not already created
        if ($this->db === null) {
            // Database connection details
            $host = 'localhost';
            $dbname = 'cook_your_language';
            $charset = 'utf8';
            $username = 'root';
            $password = 'root';

            // Create a new PDO instance
            $this->db = new \PDO(
                "mysql:host=$host;dbname=$dbname;charset=$charset",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
                ],
            );
        }

        // Return the PDO instance
        return $this->db;
    }

    /**
     * Retrieves user data from the database based on the user ID.
     *
     * @param  mixed $userId The ID of the user.
     * @return array An array containing user data.
     */
    public function getUserDataFromDatabase(int $userId): array
    {
        // Prepare and execute a query to retrieve serialized user data
        $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Fetch the serialized user data from the database
        $serializedUserData = $stmt->fetchColumn();

        // Initialize an array for session data
        $sessionData = [];

        // Check if serialized data is found in the database
        if ($serializedUserData !== false) {
            // Unserialize the data
            $sessionData = unserialize($serializedUserData);

            // Ensure it is an array
            if (!is_array($sessionData)) {
                $sessionData = [];
            }
        }

        // Return the user data
        return $sessionData;
    }
}
