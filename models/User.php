<?php

class User {
    public $id;
    public $username;
    public $password;

    // Make the constructor parameters optional
    public function __construct($id = null, $username = null, $password = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }
}

