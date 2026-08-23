<?php
class User {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    public function findByEmail($email) {
        // Find user by email database query
    }

    public function createUser($data) {
        // Insert new user into database
    }
}
