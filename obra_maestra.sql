CREATE DATABASE obra_maestra;

USE obra_maestra;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_first_time BOOLEAN NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    terms_of_service TEXT DEFAULT NULL,
    commission_type VARCHAR(255) DEFAULT NULL,
    min_price INT DEFAULT 0,
    max_price INT DEFAULT 0,
    delivery_time INT DEFAULT 0,
    slots INT DEFAULT 0,
    additional_info TEXT DEFAULT NULL,
    sample_images TEXT DEFAULT NULL,  
    notifications TEXT DEFAULT NULL,
    currency VARCHAR(10) DEFAULT 'USD',
    paypal_email VARCHAR(255) DEFAULT NULL   
);
