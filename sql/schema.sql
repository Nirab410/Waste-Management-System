CREATE DATABASE ecowaste;
USE ecowaste;

-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMIN','RESIDENT','COLLECTOR','CENTER_CONTROLLER') NOT NULL,
    phone VARCHAR(20),
    zone VARCHAR(50),
    area VARCHAR(50),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- LOGIN SESSIONS (Faculty requirement)
CREATE TABLE user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- WASTE TYPES
CREATE TABLE waste_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- RECYCLING CENTERS
CREATE TABLE recycling_centers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    zone VARCHAR(50) NOT NULL,
    max_capacity DECIMAL(10,2),
    current_capacity DECIMAL(10,2) DEFAULT 0,
    status ENUM('RUNNING','CLOSED','MAINTENANCE') DEFAULT 'RUNNING'
);

-- WASTE REQUESTS
CREATE TABLE waste_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    center_id INT NOT NULL,
    waste_type_id INT NOT NULL,
    estimated_weight DECIMAL(10,2),
    frequency ENUM('ONE_TIME','DAILY','WEEKLY'),
    status ENUM('PENDING','ON_THE_WAY','COLLECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resident_id) REFERENCES users(id),
    FOREIGN KEY (center_id) REFERENCES recycling_centers(id),
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id)
);

-- COLLECTOR ASSIGNMENT
CREATE TABLE collector_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    collector_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id),
    FOREIGN KEY (collector_id) REFERENCES users(id)
);

-- COLLECTION PROOF
CREATE TABLE collection_proofs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    image_path VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id)
);

-- CHAT MESSAGES
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id),
    FOREIGN KEY (sender_id) REFERENCES users(id)
);

-- FEEDBACK & RATING
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNIQUE,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES waste_requests(id)
);

-- DAILY COLLECTION LOG
CREATE TABLE daily_collection_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    center_id INT,
    waste_type_id INT,
    total_weight DECIMAL(10,2),
    log_date DATE,
    FOREIGN KEY (center_id) REFERENCES recycling_centers(id),
    FOREIGN KEY (waste_type_id) REFERENCES waste_types(id)
);
