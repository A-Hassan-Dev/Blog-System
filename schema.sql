CREATE DATABASE phpblog;
USE phpblog;

-- Table 1: Categories (to organize posts)
CREATE TABLE IF NOT EXISTS categories (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(50) UNIQUE NOT NULL,
 description TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- Table 2: Posts (the blog articles)
CREATE TABLE IF NOT EXISTS posts (
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(200) NOT NULL,
 content TEXT NOT NULL,
 image VARCHAR(255),
 category_id INT NOT NULL,
 views INT DEFAULT 0,
 status ENUM('draft', 'published') DEFAULT 'draft',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Table 3: Comments (user feedback on posts)
CREATE TABLE IF NOT EXISTS comments (
 id INT AUTO_INCREMENT PRIMARY KEY,
 post_id INT NOT NULL,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(100) NOT NULL,
 comment TEXT NOT NULL,
 status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'author') DEFAULT 'author',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role) 
VALUES ('Admin', 'admin@blog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

--  admin form link => localhost:8000/admin/index.php

-- username ==> admin@blog.com
-- password ==>  password