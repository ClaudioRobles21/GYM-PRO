-- ============================================================
-- GYM MANAGEMENT SYSTEM - DATABASE SCRIPT
-- Import this in phpMyAdmin or run via MySQL CLI
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS gym_system;
CREATE DATABASE gym_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gym_system;

-- ============================================================
-- TABLE: roles
-- ============================================================
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO roles (name) VALUES ('admin'), ('client');

-- ============================================================
-- TABLE: plans
-- ============================================================
CREATE TABLE plans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration_months INT NOT NULL DEFAULT 1,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO plans (name, description, price, duration_months) VALUES
('Basic', 'Access to gym floor, locker rooms and basic equipment.', 199.00, 1),
('Premium', 'Full access including group classes, sauna and personal trainer session.', 399.00, 1),
('Elite', 'Unlimited access 24/7, nutrition coaching, priority booking and guest passes.', 699.00, 1);

-- ============================================================
-- TABLE: benefits
-- ============================================================
CREATE TABLE benefits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(200) NOT NULL
) ENGINE=InnoDB;

INSERT INTO benefits (description) VALUES
('Gym floor access'),
('Locker rooms'),
('Basic equipment'),
('Group fitness classes'),
('Sauna & steam room'),
('1 personal trainer session/month'),
('24/7 access'),
('Nutrition coaching'),
('Guest passes (2/month)'),
('Priority class booking');

-- ============================================================
-- TABLE: plan_benefits (N:N)
-- ============================================================
CREATE TABLE plan_benefits (
    plan_id INT UNSIGNED NOT NULL,
    benefit_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (plan_id, benefit_id),
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    FOREIGN KEY (benefit_id) REFERENCES benefits(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO plan_benefits (plan_id, benefit_id) VALUES
(1,1),(1,2),(1,3),
(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),
(3,1),(3,2),(3,3),(3,4),(3,5),(3,6),(3,7),(3,8),(3,9),(3,10);

-- ============================================================
-- TABLE: cards
-- ============================================================
CREATE TABLE cards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    card_number VARCHAR(16) NOT NULL,
    cvv VARCHAR(4) NOT NULL,
    expiry_date DATE NOT NULL
) ENGINE=InnoDB;

INSERT INTO cards (card_number, cvv, expiry_date) VALUES
('4111111111111111', '123', '2027-06-30'),
('5500005555555559', '456', '2026-12-31'),
('4012888888881881', '789', '2028-03-31');

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    birth_date DATE,
    phone VARCHAR(20),
    photo_url VARCHAR(255) DEFAULT NULL,
    role_id INT UNSIGNED NOT NULL DEFAULT 2,
    card_id INT UNSIGNED DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- All demo accounts use password: "password"
-- Hash: bcrypt cost 10, compatible with PHP password_verify()
INSERT INTO users (name, email, password, birth_date, phone, role_id, card_id) VALUES
('Admin User',   'admin@gym.com',   '$2y$10$qtDn46P/LKVlOGEPFY.preq8QcIfLhi3Zb3z1gg5J5YsoO0VOginK', '1985-03-15', '8180001111', 1, NULL),
('John Doe',     'john@email.com',  '$2y$10$qtDn46P/LKVlOGEPFY.preq8QcIfLhi3Zb3z1gg5J5YsoO0VOginK', '1993-07-22', '8181112222', 2, 1),
('Maria Garcia', 'maria@email.com', '$2y$10$qtDn46P/LKVlOGEPFY.preq8QcIfLhi3Zb3z1gg5J5YsoO0VOginK', '1998-11-05', '8182223333', 2, 2);

-- ============================================================
-- TABLE: branches
-- ============================================================
CREATE TABLE branches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    schedule VARCHAR(200),
    admin_id INT UNSIGNED,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO branches (name, address, city, phone, schedule, admin_id) VALUES
('GymPro Centro',  'Av. Constitución 1500, Centro', 'Monterrey', '8183001000', 'Mon–Fri 5am–11pm, Sat–Sun 7am–9pm', 1),
('GymPro FIME',    'Pedro de Alba S/N, Ciudad Universitaria', 'San Nicolás de los Garza', '8183001001', 'Mon–Fri 6am–10pm, Sat 8am–6pm', 1);

-- ============================================================
-- TABLE: subscriptions
-- ============================================================
CREATE TABLE subscriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id)
) ENGINE=InnoDB;

INSERT INTO subscriptions (user_id, plan_id, start_date, end_date, status) VALUES
(2, 2, '2025-01-01', '2025-12-31', 'active'),
(3, 1, '2025-03-01', '2025-09-30', 'active');

-- ============================================================
-- TABLE: payments
-- ============================================================
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,
    card_id INT UNSIGNED,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id),
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO payments (user_id, plan_id, card_id, amount, payment_date) VALUES
(2, 2, 1, 399.00, '2025-01-01'),
(2, 2, 1, 399.00, '2025-02-01'),
(2, 2, 1, 399.00, '2025-03-01'),
(3, 1, 2, 199.00, '2025-03-01'),
(3, 1, 2, 199.00, '2025-04-01');

-- ============================================================
-- TABLE: notifications
-- ============================================================
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO notifications (title, message, created_by) VALUES
('Welcome to GymPro!', 'Thank you for joining GymPro. Your fitness journey starts today. Check our plans and branches.', 1),
('Holiday Schedule', 'On May 1st (Labor Day) all branches will operate from 9am to 3pm. Plan accordingly.', 1),
('New Class: CrossFit', 'We have added CrossFit sessions every Tuesday and Thursday at 7pm. Premium and Elite members can book now.', 1);

-- ============================================================
-- TABLE: notifications_sent (N:N)
-- ============================================================
CREATE TABLE notifications_sent (
    user_id INT UNSIGNED NOT NULL,
    notification_id INT UNSIGNED NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, notification_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO notifications_sent (user_id, notification_id, is_read) VALUES
(2, 1, 1), (2, 2, 0), (2, 3, 0),
(3, 1, 1), (3, 2, 1), (3, 3, 0);

SET FOREIGN_KEY_CHECKS = 1;
