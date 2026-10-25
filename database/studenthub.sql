-- StudentHub Database Schema & Seed Data
-- CHAROTAR UNIVERSITY OF SCIENCE AND TECHNOLOGY (CHARUSAT)
-- Subject: ITUE203 - Web Development Frameworks

CREATE DATABASE IF NOT EXISTS `studenthub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

-- 1. Users Table (Authentication, RBAC, Remember-Me)
DROP TABLE IF EXISTS `remember_tokens`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  `full_name` VARCHAR(100) NOT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Students Table (Student Management CRUD)
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `student_id` VARCHAR(20) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(15) NOT NULL,
  `department` VARCHAR(50) NOT NULL,
  `semester` INT NOT NULL DEFAULT 3,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `status` ENUM('active', 'inactive', 'deleted') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. Events Table (Event Management CRUD & Posters)
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `venue` VARCHAR(100) NOT NULL,
  `poster_image` VARCHAR(255) DEFAULT 'default_event.jpg',
  `capacity` INT NOT NULL DEFAULT 100,
  `status` ENUM('upcoming', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
  `created_by` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_events_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Registrations Table
CREATE TABLE `registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('confirmed', 'waitlisted', 'cancelled') NOT NULL DEFAULT 'confirmed',
  CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reg_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uniq_student_event` (`student_id`, `event_id`)
) ENGINE=InnoDB;

-- 5. Audit Logs Table (Practical 14)
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. Remember Tokens Table (Practical 10)
CREATE TABLE `remember_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token_hash` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_remember_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- SEED DATA
-- Default Passwords: Password123!
-- Hashed using BCRYPT (cost 12)
-- Seed hash is generated with PHP password_hash() and verifies against Password123!.
-- ==========================================================

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `full_name`, `status`) VALUES
(1, 'admin', 'admin@charusat.edu.in', '$2y$12$KtEyVRmzzVqSorqKiFsxye7gBMihgAtJWXBV0k6Bdm2rsHeym0fUa', 'admin', 'System Administrator', 'active'),
(2, 'divyakumar', 'divyakumar@charusat.edu.in', '$2y$12$KtEyVRmzzVqSorqKiFsxye7gBMihgAtJWXBV0k6Bdm2rsHeym0fUa', 'student', 'Divyakumar Shukla', 'active'),
(3, 'priya_patel', 'priya.patel@charusat.edu.in', '$2y$12$KtEyVRmzzVqSorqKiFsxye7gBMihgAtJWXBV0k6Bdm2rsHeym0fUa', 'student', 'Priya Patel', 'active');

-- Insert at least 20 students for Practical 11
INSERT INTO `students` (`id`, `user_id`, `student_id`, `first_name`, `last_name`, `email`, `phone`, `department`, `semester`, `gender`, `status`) VALUES
(1, 2, '23IT001', 'Divyakumar', 'Shukla', 'divyakumar@charusat.edu.in', '9876543210', 'Information Technology', 3, 'Male', 'active'),
(2, 3, '23IT002', 'Priya', 'Patel', 'priya.patel@charusat.edu.in', '9876543211', 'Computer Engineering', 3, 'Female', 'active'),
(3, NULL, '23IT003', 'Aarav', 'Shah', 'aarav.shah@charusat.edu.in', '9876543212', 'Information Technology', 3, 'Male', 'active'),
(4, NULL, '23IT004', 'Ananya', 'Desai', 'ananya.desai@charusat.edu.in', '9876543213', 'Computer Science', 3, 'Female', 'active'),
(5, NULL, '23IT005', 'Rohan', 'Mehta', 'rohan.mehta@charusat.edu.in', '9876543214', 'Information Technology', 3, 'Male', 'active'),
(6, NULL, '23IT006', 'Diya', 'Joshi', 'diya.joshi@charusat.edu.in', '9876543215', 'AI & ML', 3, 'Female', 'active'),
(7, NULL, '23IT007', 'Kabir', 'Dave', 'kabir.dave@charusat.edu.in', '9876543216', 'Computer Engineering', 3, 'Male', 'active'),
(8, NULL, '23IT008', 'Isha', 'Trivedi', 'isha.trivedi@charusat.edu.in', '9876543217', 'Information Technology', 3, 'Female', 'active'),
(9, NULL, '23IT009', 'Aditya', 'Panchal', 'aditya.p@charusat.edu.in', '9876543218', 'AI & ML', 3, 'Male', 'active'),
(10, NULL, '23IT010', 'Tanvi', 'Bhatt', 'tanvi.bhatt@charusat.edu.in', '9876543219', 'Computer Engineering', 3, 'Female', 'active'),
(11, NULL, '23IT011', 'Dev', 'Parmar', 'dev.parmar@charusat.edu.in', '9876543220', 'Information Technology', 3, 'Male', 'active'),
(12, NULL, '23IT012', 'Rhea', 'Sharma', 'rhea.sharma@charusat.edu.in', '9876543221', 'Computer Science', 3, 'Female', 'active'),
(13, NULL, '23IT013', 'Karan', 'Soni', 'karan.soni@charusat.edu.in', '9876543222', 'Information Technology', 3, 'Male', 'active'),
(14, NULL, '23IT014', 'Kavya', 'Rana', 'kavya.rana@charusat.edu.in', '9876543223', 'AI & ML', 3, 'Female', 'active'),
(15, NULL, '23IT015', 'Yash', 'Vyas', 'yash.vyas@charusat.edu.in', '9876543224', 'Computer Engineering', 3, 'Male', 'active'),
(16, NULL, '23IT016', 'Sneha', 'Chauhan', 'sneha.c@charusat.edu.in', '9876543225', 'Information Technology', 3, 'Female', 'active'),
(17, NULL, '23IT017', 'Harsh', 'Patel', 'harsh.p@charusat.edu.in', '9876543226', 'Computer Science', 3, 'Male', 'active'),
(18, NULL, '23IT018', 'Khushi', 'Modi', 'khushi.modi@charusat.edu.in', '9876543227', 'Information Technology', 3, 'Female', 'active'),
(19, NULL, '23IT019', 'Vivek', 'Gajjar', 'vivek.g@charusat.edu.in', '9876543228', 'Computer Engineering', 3, 'Male', 'active'),
(20, NULL, '23IT020', 'Mansi', 'Pandya', 'mansi.p@charusat.edu.in', '9876543229', 'AI & ML', 3, 'Female', 'active');

-- Events Seed Data
INSERT INTO `events` (`id`, `title`, `category`, `description`, `event_date`, `event_time`, `venue`, `poster_image`, `capacity`, `status`, `created_by`) VALUES
(1, 'Spoural 2026 Annual Tech Fest', 'Festival', 'CHARUSAT Annual Flagship Technical and Cultural Festival with 30+ competitive events.', '2026-11-15', '09:00:00', 'Central Auditorium, CHARUSAT', 'techfest.jpg', 500, 'upcoming', 1),
(2, 'Full Stack Web Bootcamp', 'Workshop', 'Hands-on training covering HTML5, CSS3, JavaScript ES6+, PHP, and MySQL application design.', '2026-11-20', '10:00:00', 'Lab 301, CSPIT Building', 'web_bootcamp.jpg', 60, 'upcoming', 1),
(3, 'AI & GenAI Hackathon 2026', 'Hackathon', '36-hour non-stop hackathon building generative AI applications for societal challenges.', '2026-12-05', '08:30:00', 'Innovation Center, DEPSTAR', 'hackathon.jpg', 120, 'upcoming', 1),
(4, 'Cyber Security & Ethical Hacking Seminar', 'Seminar', 'Industry expert talk on modern web application security, SQL injection prevention, and XSS defensive strategies.', '2026-12-12', '14:00:00', 'Seminar Hall 1', 'cyber_sec.jpg', 150, 'upcoming', 1),
(5, 'Campus Code Relay 2026', 'Competition', 'Fast-paced algorithmic programming contest with team relay rounds.', '2026-12-18', '11:00:00', 'CS Lab Complex', 'code_relay.jpg', 80, 'upcoming', 1);

-- Registrations Seed Data
INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `status`) VALUES
(1, 1, 1, 'confirmed'),
(2, 1, 2, 'confirmed'),
(3, 2, 1, 'confirmed'),
(4, 2, 3, 'confirmed'),
(5, 3, 2, 'confirmed'),
(6, 4, 3, 'confirmed'),
(7, 5, 1, 'confirmed');

-- Audit Logs Seed Data
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`) VALUES
(1, 1, 'SYSTEM_INIT', 'StudentHub database initialized with core seed tables.', '127.0.0.1'),
(2, 1, 'EVENT_CREATE', 'Created event Spoural 2026 Annual Tech Fest', '127.0.0.1'),
(3, 2, 'USER_LOGIN', 'Divyakumar Shukla logged in successfully', '127.0.0.1');
