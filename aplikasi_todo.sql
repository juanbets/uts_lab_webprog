-- Menyimpan data dalam database MySQL:

-- Membuat database:
CREATE DATABASE todo_app;

-- Menggunakan database:
USE todo_app;

-- Membuat tabel users:
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255)
);

-- Membuat tabel lists:
CREATE TABLE lists (
    list_id INT AUTO_INCREMENT PRIMARY KEY,
    list_name VARCHAR(255),
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Membuat tabel tasks:
CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    task_description VARCHAR(255),
    list_id INT,
    completed BOOLEAN DEFAULT 0,
    FOREIGN KEY (list_id) REFERENCES lists(list_id)
);