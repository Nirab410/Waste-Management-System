-- =====================================================
-- Demo Operational Data
-- =====================================================
-- Sample waste requests, assignments, chats, feedback, and logs
-- This demonstrates the complete workflow
-- =====================================================

USE ecowaste;

-- =====================================================
-- WASTE REQUESTS
-- =====================================================
INSERT INTO waste_requests (resident_id, center_id, waste_type_id, request_type, frequency, collection_location, pickup_date, estimated_weight, status) VALUES
-- Pending requests
(2, 1, 1, 'NORMAL', 'ONCE', 'Road 5, House 10, Downtown', '2024-12-20', 5.5, 'PENDING'),
(3, 1, 2, 'NORMAL', 'ONCE', '123 Main Street, Downtown', '2024-12-20', 3.2, 'PENDING'),
(4, 2, 3, 'NORMAL', 'DAILY', '456 Oak Avenue, Residential', '2024-12-21', 2.8, 'PENDING'),
(5, 3, 5, 'EMERGENCY', 'ONCE', '789 Industrial Blvd, Industrial', '2024-12-19', 15.0, 'PENDING'),
(6, 4, 4, 'NORMAL', 'WEEKLY', '321 Commerce Street, Commercial', '2024-12-22', 8.5, 'PENDING'),

-- Assigned requests
(7, 1, 1, 'NORMAL', 'ONCE', 'Road 5, House 10, Mirpur', '2024-12-19', 6.0, 'ASSIGNED'),
(2, 1, 2, 'NORMAL', 'WEEKLY', 'Road 5, House 10, Downtown', '2024-12-20', 4.5, 'ASSIGNED'),

-- On the way requests
(3, 1, 3, 'NORMAL', 'ONCE', '123 Main Street, Downtown', '2024-12-20', 3.0, 'ON_THE_WAY'),

-- Collected requests
(4, 2, 1, 'NORMAL', 'ONCE', '456 Oak Avenue, Residential', '2024-12-18', 5.2, 'COLLECTED'),
(5, 3, 2, 'NORMAL', 'DAILY', '789 Industrial Blvd, Industrial', '2024-12-18', 7.8, 'COLLECTED'),
(6, 4, 3, 'NORMAL', 'ONCE', '321 Commerce Street, Commercial', '2024-12-17', 4.3, 'COLLECTED'),
(7, 1, 4, 'NORMAL', 'WEEKLY', 'Road 5, House 10, Mirpur', '2024-12-16', 12.5, 'COLLECTED');

-- =====================================================
-- COLLECTOR ASSIGNMENTS
-- =====================================================
INSERT INTO collector_assignments (request_id, collector_id, assigned_by, assigned_at, accepted_at) VALUES
-- Assigned requests
(6, 8, 9, '2024-12-18 10:30:00', '2024-12-18 10:45:00'),
(7, 8, 9, '2024-12-19 09:15:00', '2024-12-19 09:30:00'),

-- On the way (accepted)
(8, 9, 9, '2024-12-19 14:20:00', '2024-12-19 14:35:00'),

-- Collected (completed assignments)
(9, 10, 10, '2024-12-17 08:00:00', '2024-12-17 08:15:00'),
(10, 11, 11, '2024-12-17 09:30:00', '2024-12-17 09:45:00'),
(11, 12, 9, '2024-12-16 11:00:00', '2024-12-16 11:20:00'),
(12, 8, 9, '2024-12-15 10:00:00', '2024-12-15 10:15:00');

-- =====================================================
-- COLLECTION PROOFS
-- =====================================================
INSERT INTO collection_proofs (request_id, image_path, actual_weight, collected_at) VALUES
(9, '/uploads/proofs/collection_9_20241218.jpg', 5.5, '2024-12-18 15:30:00'),
(10, '/uploads/proofs/collection_10_20241218.jpg', 8.0, '2024-12-18 16:45:00'),
(11, '/uploads/proofs/collection_11_20241217.jpg', 4.5, '2024-12-17 14:20:00'),
(12, '/uploads/proofs/collection_12_20241216.jpg', 13.0, '2024-12-16 13:10:00');

-- =====================================================
-- CHAT MESSAGES
-- =====================================================
-- Request 6 chat
INSERT INTO chat_messages (request_id, sender_id, message, sent_at) VALUES
(6, 7, 'Please come after 5 PM', '2024-12-18 11:00:00'),
(6, 8, 'Okay, I will be there around 5:30 PM', '2024-12-18 11:15:00'),
(6, 7, 'Perfect, thank you!', '2024-12-18 11:20:00'),

-- Request 7 chat
(7, 2, 'Can you come earlier? I have an emergency', '2024-12-19 09:45:00'),
(7, 8, 'I can come in 30 minutes', '2024-12-19 09:50:00'),

-- Request 8 chat
(8, 3, 'I am waiting at the main gate', '2024-12-19 15:00:00'),
(8, 9, 'I am on the way, will be there in 10 minutes', '2024-12-19 15:05:00'),

-- Request 9 chat (completed)
(9, 4, 'Please collect from the back door', '2024-12-17 08:30:00'),
(9, 10, 'Got it, heading there now', '2024-12-17 08:35:00'),
(9, 10, 'Collection completed!', '2024-12-17 15:30:00');

-- =====================================================
-- FEEDBACK & RATINGS
-- =====================================================
INSERT INTO feedback (request_id, collector_id, rating, comment, created_at) VALUES
(9, 10, 5, 'Very professional service. Collector was punctual and courteous.', '2024-12-18 16:00:00'),
(10, 11, 4, 'Good service, but arrived a bit late. Overall satisfied.', '2024-12-18 17:30:00'),
(11, 12, 5, 'Excellent! Very clean collection process.', '2024-12-17 15:00:00'),
(12, 8, 5, 'Best collector ever! Highly recommended.', '2024-12-16 14:00:00');

-- =====================================================
-- DAILY COLLECTION LOG
-- =====================================================
-- Sample daily logs for the past week
INSERT INTO daily_collection_log (center_id, waste_type_id, total_weight, log_date) VALUES
-- Eco Center A - December 16-19
(1, 1, 125.5, '2024-12-16'),
(1, 2, 89.3, '2024-12-16'),
(1, 3, 45.2, '2024-12-16'),
(1, 1, 98.7, '2024-12-17'),
(1, 2, 76.4, '2024-12-17'),
(1, 4, 112.0, '2024-12-17'),
(1, 1, 134.2, '2024-12-18'),
(1, 2, 92.5, '2024-12-18'),
(1, 3, 67.8, '2024-12-18'),
(1, 1, 87.3, '2024-12-19'),
(1, 2, 65.1, '2024-12-19'),

-- Eco Center B - December 16-19
(2, 1, 78.5, '2024-12-16'),
(2, 2, 54.2, '2024-12-16'),
(2, 3, 89.6, '2024-12-16'),
(2, 1, 95.3, '2024-12-17'),
(2, 2, 67.8, '2024-12-17'),
(2, 1, 112.4, '2024-12-18'),
(2, 3, 76.9, '2024-12-18'),
(2, 1, 88.7, '2024-12-19'),
(2, 2, 59.2, '2024-12-19'),

-- Eco Center C - December 16-19
(3, 1, 156.8, '2024-12-16'),
(3, 2, 134.5, '2024-12-16'),
(3, 5, 98.2, '2024-12-16'),
(3, 1, 178.9, '2024-12-17'),
(3, 2, 145.6, '2024-12-17'),
(3, 7, 45.3, '2024-12-17'),
(3, 1, 167.4, '2024-12-18'),
(3, 2, 123.7, '2024-12-18'),
(3, 5, 112.5, '2024-12-18'),
(3, 1, 189.2, '2024-12-19'),
(3, 2, 156.3, '2024-12-19'),

-- Eco Center D - December 16-19
(4, 1, 98.6, '2024-12-16'),
(4, 2, 76.4, '2024-12-16'),
(4, 4, 134.2, '2024-12-16'),
(4, 1, 112.8, '2024-12-17'),
(4, 2, 89.5, '2024-12-17'),
(4, 6, 67.3, '2024-12-17'),
(4, 1, 125.7, '2024-12-18'),
(4, 2, 98.1, '2024-12-18'),
(4, 4, 145.6, '2024-12-18'),
(4, 1, 108.9, '2024-12-19'),
(4, 2, 87.2, '2024-12-19'),

-- Eco Center E - December 16-19
(5, 1, 67.4, '2024-12-16'),
(5, 3, 89.2, '2024-12-16'),
(5, 4, 56.8, '2024-12-16'),
(5, 1, 78.5, '2024-12-17'),
(5, 3, 95.3, '2024-12-17'),
(5, 1, 89.6, '2024-12-18'),
(5, 3, 112.4, '2024-12-18'),
(5, 4, 67.9, '2024-12-18'),
(5, 1, 76.2, '2024-12-19'),
(5, 3, 98.7, '2024-12-19');

-- =====================================================
-- WEEKLY COLLECTION SUMMARY
-- =====================================================
-- Week starting December 16, 2024 (Monday)
INSERT INTO weekly_collection_summary (center_id, waste_type_id, week_start_date, total_weight, total_collections) VALUES
-- Eco Center A - Week of Dec 16
(1, 1, '2024-12-16', 445.7, 28),
(1, 2, '2024-12-16', 323.3, 22),
(1, 3, '2024-12-16', 113.0, 15),
(1, 4, '2024-12-16', 112.0, 8),

-- Eco Center B - Week of Dec 16
(2, 1, '2024-12-16', 374.9, 24),
(2, 2, '2024-12-16', 240.4, 18),
(2, 3, '2024-12-16', 166.5, 12),

-- Eco Center C - Week of Dec 16
(3, 1, '2024-12-16', 692.3, 35),
(3, 2, '2024-12-16', 559.1, 32),
(3, 5, '2024-12-16', 210.7, 14),
(3, 7, '2024-12-16', 45.3, 3),

-- Eco Center D - Week of Dec 16
(4, 1, '2024-12-16', 445.9, 26),
(4, 2, '2024-12-16', 351.2, 21),
(4, 4, '2024-12-16', 347.1, 19),
(4, 6, '2024-12-16', 67.3, 5),

-- Eco Center E - Week of Dec 16
(5, 1, '2024-12-16', 311.7, 20),
(5, 3, '2024-12-16', 395.6, 18),
(5, 4, '2024-12-16', 124.7, 9);
