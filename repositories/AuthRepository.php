<?php

require 'models/User.php';

class AuthRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserByUsernameAndPassword($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User($row['id'], $row['username'], $row['password']);
        }
    
        return null;
    }
    
}
