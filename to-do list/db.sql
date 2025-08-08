CREATE DATABASE todo_app;
USE todo_app;

-- Tabel user
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Tabel tugas
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(50) NOT NULL,
    detail VARCHAR(250) NOT NULL,
    priority INT NOT NULL, -- 1=Merah, 2=Oranye, 3=Kuning, 4=Hijau
    deadline DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_done BOOLEAN DEFAULT 0,
    is_expired BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
