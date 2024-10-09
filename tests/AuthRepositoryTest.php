<?php

require 'repositories/AuthRepository.php';

use PHPUnit\Framework\TestCase;

class AuthRepositoryTest extends TestCase
{
    private $db;
    private $authRepository;

    protected function setUp(): void
    {
        // Mock the PDO class
        $this->db = $this->createMock(PDO::class);

        // Create an instance of AuthRepository
        $this->authRepository = new AuthRepository($this->db);
    }

    public function testGetUserByUsernameAndPasswordReturnsUserOnValidCredentials()
    {
        // Arrange
        $username = 'testuser';
        $password = 'testpassword';

        // Mock the statement and its behavior
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([$username, $password]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => 1, 'username' => $username, 'password' => $password]);

        // Set up the PDO mock to return the statement mock
        $this->db->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Act
        $user = $this->authRepository->getUserByUsernameAndPassword($username, $password);

        // Assert
        $this->assertInstanceOf(User::class, $user);
    }

    public function testGetUserByUsernameAndPasswordReturnsNullOnInvalidCredentials()
    {
        // Arrange
        $username = 'invaliduser';
        $password = 'invalidpassword';

        // Mock the statement and its behavior
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([$username, $password]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false); // No user found

        // Set up the PDO mock to return the statement mock
        $this->db->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        // Act
        $user = $this->authRepository->getUserByUsernameAndPassword($username, $password);

        // Assert
        $this->assertNull($user);
    }
}
