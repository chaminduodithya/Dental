-- ============================================
-- DENTAL MANAGEMENT SYSTEM - DATABASE SETUP
-- ============================================
-- Instructions:
-- 1. Open http://localhost/phpmyadmin
-- 2. Click "New" to create a database
-- 3. Name it: dental_db
-- 4. Click "SQL" tab
-- 5. Paste this entire file and click "Go"
-- ============================================

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS `dental_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dental_db`;

-- ============================================
-- Table: users (Patients)
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: appointments (enhanced contact_form)
-- ============================================
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  KEY `idx_status` (`status`),
  KEY `idx_date` (`date`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: admin_users
-- ============================================
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert default admin user
-- Username: admin
-- Password: admin123 (CHANGE THIS AFTER FIRST LOGIN!)
-- ============================================
INSERT IGNORE INTO `admin_users` (`username`, `password`, `email`, `full_name`) VALUES
('admin', '$2y$10$WMeC/E5t96bGERKJicJQWOI.eK01SorhGHKb1592UdQZMwFJgh7wK', 'admin@dentalcare.com', 'Admin User');
-- Note: Password is hashed using PHP password_hash()

-- ============================================
-- Insert sample appointments (for testing)
-- ============================================
INSERT INTO `appointments` (`name`, `email`, `number`, `date`, `status`, `notes`) VALUES
('John Doe', 'john@example.com', '1234567890', '2026-02-10 10:00:00', 'pending', 'Regular checkup'),
('Jane Smith', 'jane@example.com', '0987654321', '2026-02-11 14:30:00', 'confirmed', 'Teeth cleaning'),
('Mike Johnson', 'mike@example.com', '5551234567', '2026-02-05 09:00:00', 'completed', 'Root canal follow-up'),
('Sarah Williams', 'sarah@example.com', '5559876543', '2026-02-15 16:00:00', 'pending', 'Cosmetic consultation');

-- ============================================
-- Success message
-- ============================================
SELECT 'Database setup completed successfully!' as Message;
SELECT 'Default admin credentials:' as Info, 'Username: admin' as Username, 'Password: admin123' as Password;
