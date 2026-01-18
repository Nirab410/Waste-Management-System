# EcoWaste Database Documentation

## Overview
This database supports the EcoWaste waste management system, enabling role-based access control, zone-based waste collection, and comprehensive tracking of waste requests from creation to collection.

## Database Structure

The database is organized into multiple SQL files for easy implementation:

1. **01_schema.sql** - Complete database structure (tables, relationships, indexes)
2. **02_master_data.sql** - Reference data (zones, waste types)
3. **03_demo_users.sql** - Sample users for all roles and recycling centers
4. **04_demo_data.sql** - Sample operational data (requests, assignments, chats, feedback, logs)

## Installation Instructions

### Step 1: Create Database and Tables
```bash
mysql -u root -p < sql/01_schema.sql
```

### Step 2: Insert Master Data
```bash
mysql -u root -p < sql/02_master_data.sql
```

### Step 3: Insert Demo Users
```bash
mysql -u root -p < sql/03_demo_users.sql
```

### Step 4: Insert Demo Operational Data
```bash
mysql -u root -p < sql/04_demo_data.sql
```

### Or Run All at Once
```bash
mysql -u root -p < sql/01_schema.sql
mysql -u root -p < sql/02_master_data.sql
mysql -u root -p < sql/03_demo_users.sql
mysql -u root -p < sql/04_demo_data.sql
```

## Database Schema

### Core Tables

#### 1. **zones**
Geographical zones for organizing waste collection operations.
- `id` - Primary key
- `zone_code` - Unique zone identifier (e.g., ZONE-A)
- `zone_name` - Descriptive name
- `description` - Zone details

#### 2. **waste_types**
Master list of all waste types supported by the system.
- `id` - Primary key
- `name` - Waste type name (Household, Plastic, Organic, etc.)
- `description` - Waste type details

#### 3. **users**
Central user table for all roles.
- `id` - Primary key
- `full_name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `role` - ENUM: ADMIN, RESIDENT, COLLECTOR, CENTER_CONTROLLER
- `phone` - Contact number
- `zone_id` - Foreign key to zones
- `address` - User address
- `is_active` - Account status

#### 4. **recycling_centers**
Recycling center information.
- `id` - Primary key
- `name` - Center name
- `zone_id` - Foreign key to zones
- `address` - Center location
- `max_capacity` - Maximum capacity in kg
- `current_capacity` - Current usage in kg
- `status` - ENUM: RUNNING, CLOSED, MAINTENANCE
- `controller_id` - Foreign key to users (CENTER_CONTROLLER)

#### 5. **center_waste_capabilities**
Defines which waste types each center can handle.
- `center_id` - Foreign key to recycling_centers
- `waste_type_id` - Foreign key to waste_types
- Unique constraint on (center_id, waste_type_id)

#### 6. **waste_requests**
Waste collection requests from residents.
- `id` - Primary key
- `resident_id` - Foreign key to users (RESIDENT)
- `center_id` - Foreign key to recycling_centers
- `waste_type_id` - Foreign key to waste_types
- `request_type` - ENUM: NORMAL, EMERGENCY
- `frequency` - ENUM: ONCE, DAILY, WEEKLY
- `collection_location` - Pickup address
- `pickup_date` - Scheduled date
- `estimated_weight` - Estimated weight in kg
- `status` - ENUM: PENDING, ASSIGNED, ON_THE_WAY, COLLECTED, CANCELLED

#### 7. **collector_assignments**
Tracks collector assignments to requests.
- `id` - Primary key
- `request_id` - Foreign key to waste_requests (unique)
- `collector_id` - Foreign key to users (COLLECTOR)
- `assigned_by` - Foreign key to users (who assigned)
- `assigned_at` - Assignment timestamp
- `accepted_at` - Acceptance timestamp

#### 8. **collection_proofs**
Photos uploaded by collectors as proof of collection.
- `id` - Primary key
- `request_id` - Foreign key to waste_requests
- `image_path` - Path to uploaded image
- `actual_weight` - Actual collected weight
- `collected_at` - Collection timestamp

#### 9. **chat_messages**
Communication between residents and collectors.
- `id` - Primary key
- `request_id` - Foreign key to waste_requests
- `sender_id` - Foreign key to users
- `message` - Message content
- `sent_at` - Timestamp

#### 10. **feedback**
Resident feedback and ratings for collectors.
- `id` - Primary key
- `request_id` - Foreign key to waste_requests (unique)
- `collector_id` - Foreign key to users (COLLECTOR)
- `rating` - Rating 1-5
- `comment` - Feedback text
- `created_at` - Timestamp

#### 11. **daily_collection_log**
Daily records of waste collected by centers.
- `id` - Primary key
- `center_id` - Foreign key to recycling_centers
- `waste_type_id` - Foreign key to waste_types
- `total_weight` - Total weight collected
- `log_date` - Date of collection
- Unique constraint on (center_id, waste_type_id, log_date)

#### 12. **weekly_collection_summary**
Aggregated weekly data for reporting.
- `id` - Primary key
- `center_id` - Foreign key to recycling_centers
- `waste_type_id` - Foreign key to waste_types
- `week_start_date` - Week start date (Monday)
- `total_weight` - Total weight for the week
- `total_collections` - Number of collections
- Unique constraint on (center_id, waste_type_id, week_start_date)

#### 13. **user_sessions**
User login session management.
- `id` - Primary key
- `user_id` - Foreign key to users
- `session_token` - Session token
- `expires_at` - Expiration datetime
- `created_at` - Creation timestamp

## Workflow

### 1. Resident Creates Request
- Resident submits a waste request through Resident Portal
- Request stored in `waste_requests` with status `PENDING`
- Request automatically routed to appropriate recycling center based on zone

### 2. Recycling Center Assigns Collector
- Center controller views pending requests
- Assigns a collector via `collector_assignments`
- Request status updated to `ASSIGNED`
- Collector receives notification

### 3. Collector Accepts and Collects
- Collector accepts assignment (`accepted_at` updated)
- Request status updated to `ON_THE_WAY`
- Collector travels to location and collects waste
- Uploads proof photo to `collection_proofs`
- Request status updated to `COLLECTED`

### 4. Resident Provides Feedback
- After collection, resident can rate and provide feedback
- Feedback stored in `feedback` table

### 5. Daily/Weekly Logging
- System logs daily collections in `daily_collection_log`
- Weekly summaries generated in `weekly_collection_summary`

## Demo Data

The database includes comprehensive demo data:

- **6 Zones**: Zone A through Zone F
- **8 Waste Types**: Household, Plastic, Organic, Paper, Metal, Glass, E-waste, Heavy/Bulk
- **1 Admin User**
- **7 Resident Users**
- **8 Collector Users**
- **3 Center Controller Users**
- **5 Recycling Centers**
- **12 Waste Requests** (various statuses)
- **7 Collector Assignments**
- **4 Collection Proofs**
- **9 Chat Messages**
- **4 Feedback Entries**
- **Daily logs** for 4 days across all centers
- **Weekly summaries** for all centers

## Demo Login Credentials

All demo users use the same password hash (for testing only):
- Password: `password` (hashed with bcrypt)

**Admin:**
- Email: `admin@ecowaste.com`

**Residents:**
- Email: `jenny.doe@email.com`
- Email: `michael.j@email.com`
- Email: `sarah.a@email.com`

**Collectors:**
- Email: `rahim.collector@ecowaste.com`
- Email: `michael.collector@ecowaste.com`

**Center Controllers:**
- Email: `center.manager@ecowaste.com`

## Important Notes

1. **Password Security**: In production, replace all password hashes with properly hashed passwords using bcrypt or similar.

2. **Image Paths**: Collection proof image paths are placeholders. Update to match your file storage system.

3. **Zone Routing**: The system routes requests to centers based on `zone_id`. Ensure proper zone mapping.

4. **Capacity Management**: `current_capacity` in `recycling_centers` should be updated when collections are logged.

5. **Weekly Summaries**: Weekly summaries can be generated from daily logs using aggregation queries or scheduled jobs.

## Useful Queries

### Get all pending requests for a center
```sql
SELECT wr.*, u.full_name as resident_name, wt.name as waste_type
FROM waste_requests wr
JOIN users u ON wr.resident_id = u.id
JOIN waste_types wt ON wr.waste_type_id = wt.id
WHERE wr.center_id = 1 AND wr.status = 'PENDING';
```

### Get collector's assigned tasks
```sql
SELECT wr.*, ca.assigned_at, ca.accepted_at
FROM collector_assignments ca
JOIN waste_requests wr ON ca.request_id = wr.id
WHERE ca.collector_id = 8 AND wr.status != 'COLLECTED';
```

### Get center's weekly collection summary
```sql
SELECT wcs.*, wt.name as waste_type, rc.name as center_name
FROM weekly_collection_summary wcs
JOIN waste_types wt ON wcs.waste_type_id = wt.id
JOIN recycling_centers rc ON wcs.center_id = rc.id
WHERE wcs.center_id = 1 AND wcs.week_start_date = '2024-12-16';
```

### Get resident's request history
```sql
SELECT wr.*, wt.name as waste_type, wr.status, u.full_name as collector_name
FROM waste_requests wr
JOIN waste_types wt ON wr.waste_type_id = wt.id
LEFT JOIN collector_assignments ca ON wr.id = ca.request_id
LEFT JOIN users u ON ca.collector_id = u.id
WHERE wr.resident_id = 2
ORDER BY wr.created_at DESC;
```

## Support

For questions or issues with the database schema, please refer to the main project README.txt file.
