CREATE DATABASE IF NOT EXISTS weather_db;
USE weather_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

TRUNCATE TABLE users;
INSERT INTO users (username, password) VALUES ('admin', 'admin');
