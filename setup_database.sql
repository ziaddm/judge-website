-- Database setup for the grading system
-- Run this in phpMyAdmin (http://localhost/phpmyadmin)

CREATE DATABASE IF NOT EXISTS grading_system;
USE grading_system;

-- Table for judges/users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20)
);

-- Table for grades
CREATE TABLE IF NOT EXISTS grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_members TEXT,
    project_title VARCHAR(255),
    group_number VARCHAR(50),
    articulate_req INT,
    choose_tools INT,
    clear_presentation INT,
    functioned_team INT,
    total INT,
    judge_name VARCHAR(100),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert 4 judges and 1 admin (password is '123' for all, hashed with bcrypt)
-- NOTE: These are bcrypt hashes of '123' - use password_verify() in PHP to check
INSERT INTO users (username, password, role) VALUES
('judge1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'judge'),
('judge2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'judge'),
('judge3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'judge'),
('judge4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'judge'),
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
