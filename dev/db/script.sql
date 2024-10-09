-- Create the database
CREATE DATABASE IF NOT EXISTS hospital_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hospital_db;

-- Create the 'patients' table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phoneNumber VARCHAR(20) NOT NULL
);

-- Create the 'users' table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Optional: Insert some dummy data for testing
INSERT INTO patients (name, address, phoneNumber) VALUES 
('John Doe', '123 Elm St', '555-1234'),
('Jane Smith', '456 Oak St', '555-5678');

INSERT INTO users (username, password) VALUES 
('admin', 'password123'),
('leo', '123456');
