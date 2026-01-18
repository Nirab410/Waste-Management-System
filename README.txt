EcoWaste – Waste Management System
Project Explanation (For Database Design)
1. Project Overview

EcoWaste is a role-based waste management system designed to make urban waste collection more efficient, transparent, and organized.

The system connects:

Residents (users)

Waste collectors

Recycling centers

Admin

through a centralized platform where waste collection requests are created, assigned, tracked, and reported.

The entire system is zone-based, meaning recycling centers and waste collection operations are organized by geographical zones or areas.

2. User Roles and Access Control

After login, the system detects the role of the logged-in user and grants access accordingly:

Role	Accessible Portal
Admin	Admin Portal (full system access)
Resident	Resident Portal only
Waste Collector	Collector Portal only
Recycling Center Controller	Recycling Center Portal only

Each role has strictly separated responsibilities.

3. System Entry Point (home.html)

home.html is the landing page and gateway to the system.

From here, users can access four portals:

Admin Portal → ad_demo.html

Collector Portal → cd_demo.html

Resident Portal → ResidentPortal.html

Recycling Center Portal → RecyclingCenter.html

These pages represent the core functional modules of the system.

4. Core Functional Workflow (End-to-End)
Step 1: Resident Creates Waste Collection Request

A Resident can:

Request waste collection

Specify:

Waste type (household, plastic, food waste, etc.)

Request type (one-time / daily / weekly)

Collection frequency

Collection location (address / area / zone)

Submit the request through the Resident Portal

after waste picked up user can rate and write feedback about the collector

This request is stored with an initial status like:

Pending / Requested

When collector assigned status: On the Way 

when collected status: Collected

Step 2: Request Sent to Recycling Center (Zone-Based)

Each request is routed to a Recycling Center based on:

Resident location

Zone/area mapping

Recycling center operators can:

View all incoming requests for their zone

Filter by waste type and request status

Step 3: Recycling Center Assigns Waste Collector

The Recycling Center:

Assigns a Waste Collector to a specific request

Assignment triggers:

Status update (e.g., Collector Assigned)

Collector notification in Collector Portal

Resident can now see:

Collector name

Phone number

Step 4: Waste Collector Performs Collection

A Waste Collector can:

View assigned collection tasks

Accept the task

Travel to the given location

Collect waste

Update status to:

Collected

Upload:

A photo of the collected waste as proof

Step 5: Status Visibility & Communication

Resident can:

Track request status in real time

View uploaded collection photo

Chat with the assigned collector if needed

Collector and resident have direct messaging/chat support per request

Step 6: Recycling Center Reporting & Analytics

Each Recycling Center:

Maintains daily records of:

Amount of waste collected

Waste type

Automatically aggregates:

Weekly collection data by waste type collected

Waste history reports

Tracks:

Current capacity usage

Operational status

5. Recycling Center Entity Details

Each Recycling Center has the following properties:

Center name

Zone / area

Supported waste types

Maximum capacity

Current capacity usage

Status:

Running

Closed

Under maintenance

Only one Admin system exists, but multiple recycling centers can exist across different zones.

6. Admin Responsibilities

The Admin has full system control and can:

Create and manage:

Waste collectors

Recycling centers

Assign collectors to centers

Monitor:

All requests

Collection history

System performance

Manage system-wide configurations

7. Key Data Entities (Conceptual)

This system naturally requires the following core entities (tables):

User-Related

User

UserRole (Admin / Resident / Collector / CenterController)

Waste Collection:

WasteRequest

WasteType

RequestFrequency

RequestStatus

Operations:

CollectorAssignment

CollectionProof (image/photo)

ChatMessage

Give feedback & rate about collector 

Recycling Center:

RecyclingCenter

Zone

CenterWasteCapability

DailyCollectionLog

WeeklyCollectionSummary

8. Relationships (High-Level):

One Resident → many Waste Requests

One Waste Request → one Recycling Center

One Waste Request → one Collector (assigned)

One Collector → many Assignments

One Recycling Center → many Collectors

One Waste Request → many Chat Messages

One Recycling Center → many Daily Collection Records

9. Design Philosophy (Why This Matters for DB)

Role-based access → requires role mapping

Zone-based routing → requires location & zone tables

Status-driven workflow → requires request status history

Proof & transparency → requires media storage

Reporting & analytics → requires daily & weekly aggregation tables

10. Intended Outcome

The database should support:

Real-time tracking

Accountability

Scalability across zones

Clean reporting for admin and recycling centers

Clear separation of concerns between roles