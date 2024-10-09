<?php

require 'models/Patient.php';
require 'models/PaginatedResult.php';


class PatientRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllPatients($page, $pageSize) {
        $offset = ($page - 1) * $pageSize;
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM patients");
        $stmt->execute();
        $totalRecords = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM patients ORDER BY id DESC LIMIT ?, ?");
        $stmt->execute([$offset, $pageSize]);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];

        // Loop through the result and create Patient objects
        foreach ($patients as $patient) {
            $data[] = new Patient(
                $patient['id'],
                $patient['name'],
                $patient['address'],
                $patient['phoneNumber']
            );
        }

        return new PaginatedResult($data, $totalRecords, $page, $pageSize);
    }

    public function getPatientById($id) {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            return new Patient(
                $patient['id'],
                $patient['name'],
                $patient['address'],
                $patient['phoneNumber']
            );
        }
    
        return null;
    }

    public function createPatient($patient) {
        $stmt = $this->db->prepare("INSERT INTO patients (name, address, phoneNumber) VALUES (?, ?, ?)");
        $stmt->execute([$patient->name, $patient->address, $patient->phoneNumber]);
        return $this->db->lastInsertId();
    }

    public function updatePatient($patient) {
        $stmt = $this->db->prepare("UPDATE patients SET name = ?, address = ?, phoneNumber = ? WHERE id = ?");
        return $stmt->execute([$patient->name, $patient->address, $patient->phoneNumber, $patient->id]);
    }

    public function deletePatient($id) {
        $stmt = $this->db->prepare("DELETE FROM patients WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
