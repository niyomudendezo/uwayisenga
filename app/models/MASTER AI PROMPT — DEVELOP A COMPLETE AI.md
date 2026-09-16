MASTER AI PROMPT — DEVELOP A COMPLETE AI-BASED AGRICULTURE COOPERATIVE AND MARKET LINKAGE SYSTEM (PHP & MYSQL)
ROLE
You are a Senior Software Architect, Senior PHP Developer, AI Engineer, Database Architect, UI/UX Designer, DevOps Engineer, and Cybersecurity Expert with over 15 years of experience building enterprise systems.

Your mission is to develop a complete, production-ready web application called:

AI-Based Agriculture Cooperative and Market Linkage System
This is a Final Year Project for a Bachelor of Information Technology student in Rwanda.

Do not generate a simple CRUD project or demo application. Build a professional enterprise-level system that could realistically be used by agricultural cooperatives, buyers, and government agencies.

PRIMARY OBJECTIVE
Develop a secure AI-powered web platform that enables farmers and cooperatives to:

Predict crop demand.

Predict future market prices.

Identify the best buyers.

Plan crop production.

Manage harvests and inventory.

Connect directly with verified buyers.

Analyze sales and market trends.

Improve farmers' income using AI recommendations.

The project should solve real agricultural marketing problems in Rwanda.

TECHNOLOGY STACK
Frontend

HTML5

CSS3

Bootstrap 5

JavaScript (ES6)

jQuery

AJAX

Chart.js

DataTables

Backend

PHP 8.3 (Object-Oriented Programming)

MVC Architecture

PDO (Prepared Statements)

RESTful API where appropriate

Database

MySQL 8

Server

Apache (XAMPP/LAMP)

Version Control

Git

PDF Reports

Dompdf or TCPDF

Excel Reports

PhpSpreadsheet

Authentication

PHP Sessions

CSRF Protection

Password Hashing (password_hash() / password_verify())

AI Integration

Python FastAPI microservice or local Python scripts invoked from PHP for predictions (recommended), with clear API integration.

SYSTEM USERS
The system must support four roles:

Administrator

Cooperative Manager

Farmer

Buyer

Each role must have its own dashboard, permissions, and features.

MODULES
Authentication & User Management
User registration

Secure login

Logout

Forgot password

Password reset

Change password

Role-based access control (RBAC)

User profile management

Audit logs

Farmer Management
Register farmers

Manage farmer profiles

Farm details

Crop production records

Harvest history

Sales history

AI recommendations

Market price dashboard

Cooperative Management
Register cooperatives

Manage cooperative members

Crop collection

Inventory management

Warehouse tracking

Production planning

Cooperative reports

Buyer Management
Buyer registration

Buyer profiles

Search crops

Filter by district, cooperative, crop, quantity, and price

Place purchase orders

Track order status

Purchase history

Crop Management
Support multiple crops (e.g., maize, beans, potatoes, rice, cassava).

Each crop should include:

Name

Category

Variety

Quantity

Unit

Grade

Harvest date

Expiry date (if applicable)

Storage location

Images

Inventory Management
Track:

Available stock

Reserved stock

Sold stock

Damaged stock

Warehouse balances

Market Price Management
Maintain:

Daily market prices

Historical prices

District-level prices

Buyer offers

Display:

Price trend charts

Highest/lowest prices

Average prices

Seasonal trends

AI Recommendation Engine
The AI must analyze:

Historical market prices

Sales history

Crop production

Seasonal demand

Buyer demand

Inventory levels

Generate recommendations such as:

Predicted demand (High / Medium / Low)

Predicted market price

Best buyer

Best selling period

Estimated revenue

Confidence score

Suggested production quantity

Example:

Crop: Maize

Predicted Demand: High

Predicted Price: 610 RWF/kg

Best Buyer: Kigali Grain Cooperative

Expected Revenue: 1,220,000 RWF

Recommendation: Delay selling for 7 days to maximize profit.

Order Management
Workflow:

Buyer submits order

↓

Cooperative reviews

↓

Approve or Reject

↓

Payment

↓

Delivery

↓

Completed

Track every status with timestamps.

Notifications
New orders

Order updates

Market price changes

AI recommendations

Low stock alerts

System announcements

Reports
Generate PDF and Excel reports for:

Farmers

Cooperatives

Buyers

Crop production

Inventory

Orders

Revenue

Market prices

AI predictions

Include charts and summary statistics.

ADMIN DASHBOARD
Display:

Total farmers

Total cooperatives

Total buyers

Total crops

Active orders

Revenue

AI prediction summary

Market price trends

Regional production

Top-selling crops

Recent activities

Use responsive cards and interactive charts.

DATABASE DESIGN
Create a normalized MySQL database (3NF) with approximately 30 tables, including:

users

roles

permissions

farmers

cooperatives

cooperative_members

buyers

crops

crop_categories

harvests

inventories

warehouses

orders

order_items

payments

deliveries

market_prices

price_history

ai_predictions

notifications

reports

audit_logs

districts

sectors

cells

villages

settings

activity_logs

attachments

system_logs

Generate:

ER Diagram

SQL schema

Foreign keys

Indexes

Sample seed data

SECURITY REQUIREMENTS
Implement:

PDO prepared statements

Input validation

Output escaping

CSRF protection

XSS prevention

Secure session management

Password hashing

RBAC

Audit logging

File upload validation

USER INTERFACE
Design a modern, responsive web interface using Bootstrap 5.

Include:

Public landing page

About page

Contact page

Login & Registration

Dashboards

Tables with search, filter, pagination, and export

Charts

Forms with client-side and server-side validation

Dark mode (optional)

Use a clean agricultural theme with green and white colors.

PROJECT STRUCTURE
Use MVC architecture with folders such as:

app/

controllers/

models/

views/

services/

helpers/

middleware/

config/

public/

assets/

uploads/

routes/

vendor/

database/

Write clean, reusable, object-oriented PHP code.

DEVELOPMENT PROCESS
Build the project in phases:

Analyze the requirements.

Design the system architecture.

Design the database and ER diagram.

Create UML diagrams (Use Case, Activity, Sequence, Class).

Design the UI wireframes.

Set up the project structure.

Implement authentication.

Develop each module one at a time.

Integrate the AI prediction service.

Test all features.

Optimize performance.

Prepare deployment instructions and documentation.

Do not skip phases or jump directly to coding.

CODING STANDARDS
Use PHP OOP best practices.

Follow SOLID principles.

Separate business logic from presentation.

Avoid duplicated code.

Comment complex logic.

Handle errors gracefully.

Use transactions for critical database operations.

Use pagination for large datasets.

FINAL DELIVERABLES
Produce:

Complete PHP source code

Complete MySQL database

SQL installation script

ER Diagram

UML diagrams

API documentation (if applicable)

User manual

Administrator manual

Testing documentation

Deployment guide

Full academic project report

Presentation-ready screenshots

Build the system as if it will be deployed for real agricultural cooperatives in Rwanda. Every feature should be fully functional, secure, and connected to the database. Do not generate placeholder code or incomplete modules. Continue development module by module until the entire application is complete. make this project and create it then called agrukrwand and set in this opt/lampp/htdocs