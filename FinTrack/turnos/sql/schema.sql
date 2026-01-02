CREATE DATABASE IF NOT EXISTS turnos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE turnos_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  telefono VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  verification_token VARCHAR(64),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  servicio VARCHAR(100) NOT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  status ENUM('pendiente','cancelado','asistido') DEFAULT 'pendiente',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notification_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT NOT NULL,
  notification_type VARCHAR(50) NOT NULL,
  status VARCHAR(20) DEFAULT 'sent',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
