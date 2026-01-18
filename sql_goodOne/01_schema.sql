-- =====================================================
-- EcoWaste Database Schema
-- =====================================================
-- This file contains the complete database structure
-- Run this file first to create all tables
-- =====================================================

CREATE DATABASE IF NOT EXISTS ecowaste;
USE ecowaste;

-- =====================================================
-- 1. ZONES TABLE
-- =====================================================
-- Stores geographical zones for organizing waste collection
CREATE TABLE zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone_code VARCHAR(20) UNIQUE NOT NULL,
    zone_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 2. WASTE TYPES TABLE
-- =====================================================
-- Master list of all waste types supported by the system
CREATE TABLE waste_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. USERS TABLE
-- =====================================================
-- Central user table for all roles: Admin, Resident, Collector, Center Controller
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'RESIDENT', 'COLLECTOR', 'CENTER_CONTROLLER') NOT NULL,
    phone VARCHAR(20),
    zone_id INT,
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE SET NULL,
    INDEX idx_role (role),
    INDEX idx_zone (zone_id)
);

-- =====================================================
-- 4. RECYCLING CENTERS TABLE
-- =====================================================
-- Stores information about recycling centers
CREATE TABLE recycling_centers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    zone_id INT NOT NULL,
    address TEXT,
    max_capacity DECIMAL(10, 2) DEFAULT 0,
    current_capacity DECIMAL(10, 2) DEFAULT 0,
    status ENUM('RUNNING', 'CLOSED', 'MAINTENANCE') DEFAULT 'RUNNING',
    controller_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (zone_id) REFERENCES zones(id),
    FOREIGN KEY (controller_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_zone (zone_id),
    INDEX idx_status (status)
);

-- =====================================================
-- 5. CENTER WASTE CAPABILITIES TABLE
-- =====================================================
-- Defines which waste types each recycling center can handle
CREATE TABLE center_waste_capabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    center_id INT NOT NULL,
    waste_type_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES recycling_centers(id) ON DELETE CASCADE,
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_center_waste (center_id, waste_type_id)
);

-- =====================================================
-- 6. WASTE REQUESTS TABLE
-- =====================================================
-- Stores waste collection requests from residents
CREATE TABLE waste_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    center_id INT NOT NULL,
    waste_type_id INT NOT NULL,
    request_type ENUM('NORMAL', 'EMERGENCY') DEFAULT 'NORMAL',
    frequency ENUM('ONCE', 'DAILY', 'WEEKLY') DEFAULT 'ONCE',
    collection_location TEXT NOT NULL,
    pickup_date DATE,
    estimated_weight DECIMAL(10, 2),
    status ENUM('PENDING', 'ASSIGNED', 'ON_THE_WAY', 'COLLECTED', 'CANCELLED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES users(id),
    FOREIGN KEY (center_id) REFERENCES recycling_centers(id),
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id),
    INDEX idx_status (status),
    INDEX idx_resident (resident_id),
    INDEX idx_center (center_id),
    INDEX idx_created (created_at)
);

-- =====================================================
-- 7. COLLECTOR ASSIGNMENTS TABLE
-- =====================================================
-- Tracks which collector is assigned to which waste request
CREATE TABLE collector_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    collector_id INT NOT NULL,
    assigned_by INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accepted_at TIMESTAMP NULL,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (collector_id) REFERENCES users(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_request_assignment (request_id)
);

-- =====================================================
-- 8. COLLECTION PROOFS TABLE
-- =====================================================
-- Stores photos/images uploaded by collectors as proof of collection
CREATE TABLE collection_proofs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    actual_weight DECIMAL(10, 2),
    collected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id) ON DELETE CASCADE,
    INDEX idx_collected (collected_at)
);

-- =====================================================
-- 9. CHAT MESSAGES TABLE
-- =====================================================
-- Enables communication between residents and collectors per request
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    INDEX idx_request (request_id),
    INDEX idx_sent (sent_at)
);

-- =====================================================
-- 10. FEEDBACK & RATINGS TABLE
-- =====================================================
-- Stores resident feedback and ratings for collectors after collection
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNIQUE NOT NULL,
    collector_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (collector_id) REFERENCES users(id),
    INDEX idx_collector (collector_id),
    INDEX idx_rating (rating)
);

-- =====================================================
-- 11. DAILY COLLECTION LOG TABLE
-- =====================================================
-- Daily records of waste collected by recycling centers
CREATE TABLE daily_collection_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    center_id INT NOT NULL,
    waste_type_id INT NOT NULL,
    total_weight DECIMAL(10, 2) NOT NULL,
    log_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES recycling_centers(id),
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id),
    UNIQUE KEY unique_daily_log (center_id, waste_type_id, log_date),
    INDEX idx_date (log_date),
    INDEX idx_center_date (center_id, log_date)
);

-- =====================================================
-- 12. WEEKLY COLLECTION SUMMARY TABLE
-- =====================================================
-- Aggregated weekly data for reporting and analytics
CREATE TABLE weekly_collection_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    center_id INT NOT NULL,
    waste_type_id INT NOT NULL,
    week_start_date DATE NOT NULL,
    total_weight DECIMAL(10, 2) NOT NULL,
    total_collections INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES recycling_centers(id),
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id),
    UNIQUE KEY unique_weekly_summary (center_id, waste_type_id, week_start_date),
    INDEX idx_week (week_start_date)
);

-- =====================================================
-- 13. USER SESSIONS TABLE
-- =====================================================
-- Manages user login sessions
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (session_token),
    INDEX idx_expires (expires_at)
);
