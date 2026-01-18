-- =====================================================
-- Master Data - Lookup Tables
-- =====================================================
-- This file contains reference data: zones, waste types
-- Run this after schema.sql
-- =====================================================

USE ecowaste;

-- =====================================================
-- INSERT ZONES
-- =====================================================
INSERT INTO zones (zone_code, zone_name, description) VALUES
('ZONE-A', 'Zone A - Downtown', 'Central business district and downtown area'),
('ZONE-B', 'Zone B - Residential', 'Residential neighborhoods and housing areas'),
('ZONE-C', 'Zone C - Industrial', 'Industrial zones and manufacturing areas'),
('ZONE-D', 'Zone D - Commercial', 'Commercial areas and shopping districts'),
('ZONE-E', 'Zone E - Suburban', 'Suburban residential areas'),
('ZONE-F', 'Zone F - University', 'University campus and student housing areas');

-- =====================================================
-- INSERT WASTE TYPES
-- =====================================================
INSERT INTO waste_types (name, description) VALUES
('Household Waste', 'General household waste and mixed garbage'),
('Plastic', 'Plastic bottles, containers, and packaging materials'),
('Organic', 'Food waste, kitchen scraps, and biodegradable materials'),
('Paper', 'Newspapers, cardboard, and paper products'),
('Metal', 'Metal cans, aluminum, and scrap metal'),
('Glass', 'Glass bottles and containers'),
('E-waste', 'Electronic waste including old devices and batteries'),
('Heavy/Bulk', 'Large items like furniture, appliances, and construction waste');
