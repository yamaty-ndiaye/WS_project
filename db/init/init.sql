CREATE DATABASE IF NOT EXISTS db_boissons;
USE db_boissons;

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    price INT,
    image_url TEXT,
    url TEXT,
    source VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);