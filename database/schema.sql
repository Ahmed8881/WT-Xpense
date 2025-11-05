-- Create Database
CREATE DATABASE IF NOT EXISTS expense_tracker;
USE expense_tracker;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Incomes Table
CREATE TABLE incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    date DATE NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Expenses Table
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    amount DECIMAL(12,2) NOT NULL,
    date DATE NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Default Admin User (username: admin, password: admin123)
-- Password hash generated with: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password) VALUES ('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFCc4D4iGvf6cM10Dq6Zy8s9WaJCn6HW');

-- Insert Sample Categories for admin user (user_id = 1)
INSERT INTO categories (name, user_id) VALUES 
('Food', 1),
('Transport', 1),
('Entertainment', 1),
('Utilities', 1),
('Healthcare', 1),
('Shopping', 1);
