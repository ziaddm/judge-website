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

-- Insert 4 judges and 1 admin (password is '123' for all)
INSERT INTO users (username, password, role) VALUES
('judge1', '123', 'judge'),
('judge2', '123', 'judge'),
('judge3', '123', 'judge'),
('judge4', '123', 'judge'),
('admin', '123', 'admin');
