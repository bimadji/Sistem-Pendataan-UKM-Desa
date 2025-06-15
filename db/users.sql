-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add admin user with password: admin123
INSERT INTO users (username, password, name, email, role) VALUES
('admin', '$2y$10$x5L.E0O4bCwPj4VyAXUVgOCPW.zz57bvXnxl3FJ4z0pTwfcZNUaVC', 'Administrator', 'admin@sipudesa.id', 'admin');

-- Add user_id to ukm table to track which user created the UKM
ALTER TABLE ukm ADD COLUMN user_id INT;
ALTER TABLE ukm ADD CONSTRAINT fk_ukm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Add visits counter to ukm table to track popularity
ALTER TABLE ukm ADD COLUMN visits INT DEFAULT 0; 