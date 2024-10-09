<?php

use PHPUnit\Framework\TestCase;

require 'repositories/PatientRepository.php';

class PatientRepositoryTest extends TestCase
{
    private $db;
    private $patientRepository;

    protected function setUp(): void
    {
        // Mock the PDO class
        $this->db = $this->createMock(PDO::class);
        $this->patientRepository = new PatientRepository($this->db);
    }

    public function testGetAllPatientsReturnsPaginatedResult()
    {
        // Arrange
        $page = 1;
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        // Mock the COUNT statement
        $countStmt = $this->createMock(PDOStatement::class);
        $countStmt->expects($this->once())
            ->method('execute');
        $countStmt->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(100); // Total records

        // Mock the SELECT statement
        $selectStmt = $this->createMock(PDOStatement::class);
        $selectStmt->expects($this->once())
            ->method('execute')
            ->with([$offset, $pageSize]);
        $selectStmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['id' => 1, 'name' => 'John Doe', 'address' => '123 Main St', 'phoneNumber' => '555-1234'],
                // Add more mock patients as needed
            ]);

        // Set up the PDO mock to return the statement mocks
        $this->db->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($countStmt, $selectStmt);

        // Act
        $result = $this->patientRepository->getAllPatients($page, $pageSize);

        // Assert
        $this->assertInstanceOf(PaginatedResult::class, $result);
    }

    public function testGetPatientByIdReturnsPatient()
    {
        // Arrange
        $id = 1;

        // Mock the statement
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([$id]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'name' => 'John Doe', 'address' => '123 Main St', 'phoneNumber' => '555-1234']);

        // Set up the PDO mock to return the statement mock
        $this->db->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Act
        $patient = $this->patientRepository->getPatientById($id);

        // Assert
        $this->assertInstanceOf(Patient::class, $patient);
    }

    public function testGetPatientByIdReturnsNullWhenNotFound()
    {
        // Arrange
        $id = 999;

        // Mock the statement
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([$id]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false); // No patient found

        // Set up the PDO mock to return the statement mock
        $this->db->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Act
        $patient = $this->patientRepository->getPatientById($id);

        // Assert
        $this->assertNull($patient);
    }

    public function testUpdatePatientReturnsTrue()
    {
        // Arrange
        $patient = new Patient(1, 'John Doe', '123 Main St', '555-1234');

        // Mock the statement
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([$patient->name, $patient->address, $patient->phoneNumber, $patient->id])
            ->willReturn(true);

        // Set up the PDO mock to return the statement mock
        $this->db->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Act
        $result = $this->patientRepository->updatePatient($patient);

        // Assert
        $this->assertTrue($result);
    }

    public function testDeletePatientReturnsTrue()
    {
        // Arrange
        $id = 1;

        // Mock the statement
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([$id])
            ->willReturn(true);

        // Set up the PDO mock to return the statement mock
        $this->db->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Act
        $result = $this->patientRepository->deletePatient($id);

        // Assert
        $this->assertTrue($result);
    }
}
