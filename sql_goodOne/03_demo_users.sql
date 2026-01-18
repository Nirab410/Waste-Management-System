-- =====================================================
-- Demo Users Data
-- =====================================================
-- Sample users for all roles with demo data
-- Password hash placeholder: In production, use proper password hashing
-- =====================================================

USE ecowaste;

-- =====================================================
-- ADMIN USERS
-- =====================================================
INSERT INTO users (full_name, email, password, role, phone, zone_id, address) VALUES
('Admin User', 'admin@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', '01700000000', NULL, 'System Headquarters');

-- =====================================================
-- RESIDENT USERS
-- =====================================================
INSERT INTO users (full_name, email, password, role, phone, zone_id, address) VALUES
('Jenny Doe', 'jenny.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RESIDENT', '+880 1712 345 678', 1, 'Road 5, House 10, Downtown'),
('Michael Johnson', 'michael.j@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RESIDENT', '+1 (555) 123-4567', 1, '123 Main Street, Downtown'),
('Sarah Anderson', 'sarah.a@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RESIDENT', '+1 (555) 987-6543', 2, '456 Oak Avenue, Residential'),
('David Lee', 'david.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RESIDENT', '+1 (555) 456-7800', 3, '789 Industrial Blvd, Industrial'),
('Emily Brown', 'emily.b@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RESIDENT', '+0 (555) 321-0987', 4, '321 Commerce Street, Commercial'),
('James Wilson', 'james.w@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RESIDENT', '+1 (555) 654-3210', 5, '654 Suburban Lane, Suburban'),
('Rahim Mia', 'rahim.mia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RESIDENT', '01811111111', 1, 'Road 5, House 10, Mirpur');

-- =====================================================
-- WASTE COLLECTOR USERS
-- =====================================================
INSERT INTO users (full_name, email, password, role, phone, zone_id, address) VALUES
('Rahim Mia', 'rahim.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '01521 2333 4544', 1, 'Collector Base Station A'),
('Michael Johnson', 'michael.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '+1 (555) 123-4567', 1, 'Downtown Collection Point'),
('Sarah Anderson', 'sarah.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '+1 (555) 987-6543', 2, 'Residential Collection Point'),
('Karim Collector', 'karim.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '01922222222', 1, 'Zone A Collection Hub'),
('Emily Brown', 'emily.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '+0 (555) 321-0987', 4, 'Commercial Collection Point'),
('John Smith', 'john.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '+1 (555) 111-2222', 3, 'Industrial Collection Point'),
('Lisa Chen', 'lisa.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '+1 (555) 333-4444', 5, 'Suburban Collection Point'),
('Robert Taylor', 'robert.collector@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'COLLECTOR', '+1 (555) 555-6666', 6, 'University Collection Point');

-- =====================================================
-- RECYCLING CENTER CONTROLLER USERS
-- =====================================================
INSERT INTO users (full_name, email, password, role, phone, zone_id, address) VALUES
('Center Manager', 'center.manager@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CENTER_CONTROLLER', '01633333333', 1, 'Eco Center A Office'),
('Ahmed Hassan', 'ahmed.center@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CENTER_CONTROLLER', '01744444444', 2, 'Eco Center B Office'),
('Fatima Ali', 'fatima.center@ecowaste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CENTER_CONTROLLER', '01855555555', 3, 'Eco Center C Office');

-- =====================================================
-- RECYCLING CENTERS
-- =====================================================
INSERT INTO recycling_centers (name, zone_id, address, max_capacity, current_capacity, status, controller_id) VALUES
('Eco Center A', 1, '123 Recycling Road, Downtown', 10000.00, 3500.00, 'RUNNING', 9),
('Eco Center B', 2, '456 Green Street, Residential', 8000.00, 2200.00, 'RUNNING', 10),
('Eco Center C', 3, '789 Waste Management Ave, Industrial', 15000.00, 8500.00, 'RUNNING', 11),
('Eco Center D', 4, '321 Commerce Recycling, Commercial', 12000.00, 4200.00, 'RUNNING', 9),
('Eco Center E', 5, '654 Suburban Recycling Hub', 9000.00, 1800.00, 'RUNNING', 10);

-- =====================================================
-- CENTER WASTE CAPABILITIES
-- =====================================================
-- Eco Center A capabilities
INSERT INTO center_waste_capabilities (center_id, waste_type_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6);

-- Eco Center B capabilities
INSERT INTO center_waste_capabilities (center_id, waste_type_id) VALUES
(2, 1), (2, 2), (2, 3), (2, 4);

-- Eco Center C capabilities
INSERT INTO center_waste_capabilities (center_id, waste_type_id) VALUES
(3, 1), (3, 2), (3, 5), (3, 7), (3, 8);

-- Eco Center D capabilities
INSERT INTO center_waste_capabilities (center_id, waste_type_id) VALUES
(4, 1), (4, 2), (4, 4), (4, 6);

-- Eco Center E capabilities
INSERT INTO center_waste_capabilities (center_id, waste_type_id) VALUES
(5, 1), (5, 3), (5, 4);
