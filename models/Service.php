<?php
class Service {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    public function getAllServices() {
        // Query to get all nail services
    }

    public function getServiceById($id) {
        // Query to get service by ID
    }
}
