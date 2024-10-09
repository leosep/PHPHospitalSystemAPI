<?php

class Patient {
    public $id;
    public $name;
    public $address;
    public $phoneNumber;

    public function __construct($id, $name, $address, $phoneNumber) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
    }
}
