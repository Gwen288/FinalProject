create database Ashesi_Student_Portfolio_System;

use Ashesi_Student_Portfolio_System;




-- Users Table
CREATE TABLE PortfolioHub_Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('student','admin') DEFAULT 'student',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Student Profile Table
CREATE TABLE Student_Profile (
    profile_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    bio TEXT,
    major VARCHAR(100) NOT NULL,
    year INT ,
    profile_picture VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES PortfolioHub_Users(user_id) ON DELETE CASCADE
);

-- Category Table
CREATE TABLE Category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

-- Portfolio Table
CREATE TABLE Portfolio (
    portfolio_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    category_id INT NOT NULL,
    profile_image VARCHAR(255) NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    visibility ENUM('public','private') DEFAULT 'public',
    status ENUM('active','inactive','archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES PortfolioHub_Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(category_id)
);

-- Portfolio Item Table
CREATE TABLE Portfolio_Item (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    item_type ENUM('Projects','Achievements','Skills','Certifications','Experiences') NOT NULL,
    title VARCHAR(100),
    description TEXT NOT NULL,
    location VARCHAR(150),
    role VARCHAR(100),
    start_date DATE,
    end_date DATE,
    date_received DATE,
    attachment VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    FOREIGN KEY (portfolio_id) REFERENCES Portfolio(portfolio_id) ON DELETE CASCADE
);

-- Admin Log Table
CREATE TABLE Admin_Log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action_type ENUM('create','update','delete','archive','deactivate') NOT NULL,
    description VARCHAR(150) NOT NULL,
    target_table VARCHAR(50) NOT NULL,
    target_id INT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES PortfolioHub_Users(user_id) ON DELETE CASCADE
);

-- Sample Category Inserts
INSERT INTO Category (category_name) VALUES
('General'),
('Technology'),
('Business & Finance'),
('Engineering'),
('Design & Arts'),
('Research & Academic');


