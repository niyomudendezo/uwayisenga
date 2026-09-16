# AGRUKRWANDA — RESEARCH CHAPTERS
# AI-Powered Agricultural Cooperative and Market Linkage System
# Rwanda

---

# CHAPTER ONE: INTRODUCTION

## 1.1 Background of the Study

Agriculture is the backbone of Rwanda's economy, employing over 70% of the population and contributing approximately 25% to the Gross Domestic Product (GDP) (MINAGRI, 2020). Despite this significance, smallholder farmers continue to face persistent challenges including limited access to market information, poor linkage between producers and buyers, post-harvest losses due to poor inventory management, and lack of data-driven decision-making tools. These challenges result in low farm incomes, food insecurity, and inefficient agricultural value chains.

Agricultural cooperatives in Rwanda play a central role in organizing smallholder farmers, pooling resources, and facilitating collective marketing. However, most cooperatives still rely on manual record-keeping, informal price negotiations, and word-of-mouth market information. This limits their ability to respond to market dynamics, plan production effectively, and connect with verified buyers at competitive prices.

The emergence of Artificial Intelligence (AI) and web-based technologies presents a transformative opportunity to address these challenges. AI-powered systems can analyze historical market data, predict future prices and demand, and provide actionable recommendations to farmers and cooperative managers. When integrated with a digital market linkage platform, such systems can eliminate information asymmetry, reduce the role of exploitative middlemen, and create transparent, efficient agricultural markets.

AgruKrwanda is an AI-powered Agricultural Cooperative and Market Linkage System developed to address these challenges in the Rwandan context. The system connects farmers, cooperative managers, and buyers through a unified web platform that provides real-time market prices, AI-driven demand and price predictions, inventory management, order processing, and comprehensive reporting. By digitizing the entire agricultural value chain from harvest recording to order fulfillment, AgruKrwanda aims to increase farmer incomes, improve cooperative efficiency, and strengthen Rwanda's agricultural market infrastructure.

## 1.2 Problem Statement

Despite Rwanda's significant investment in agricultural transformation through policies such as the Strategic Plan for the Transformation of Agriculture in Rwanda (PSTA IV) and the National Agricultural Policy (NAP), smallholder farmers and agricultural cooperatives continue to face critical challenges that undermine productivity and market participation:

1. Information asymmetry: Farmers lack access to real-time and accurate market price information, forcing them to sell at prices dictated by middlemen who exploit information gaps.

2. Weak market linkages: There is no structured digital platform connecting cooperatives directly with verified buyers, resulting in long, inefficient supply chains with multiple intermediaries.

3. Poor inventory management: Cooperatives manage their post-harvest inventory manually, leading to inaccurate stock records, spoilage, and missed sales opportunities.

4. Absence of predictive tools: Farmers and cooperative managers make production and selling decisions without data-driven insights, resulting in oversupply, undersupply, and price volatility.

5. Limited administrative oversight: Agricultural administrators lack centralized tools to monitor cooperative performance, farmer activities, market trends, and system-wide analytics.

These problems collectively reduce the competitiveness of Rwandan agricultural cooperatives and limit the income potential of smallholder farmers. There is therefore a need for an integrated, AI-powered digital system that addresses all these challenges in a unified platform.

## 1.3 Objectives of the Study

### 1.3.1 General Objective

The general objective of this study is to design and develop an AI-powered Agricultural Cooperative and Market Linkage System (AgruKrwanda) that connects farmers, cooperatives, and buyers in Rwanda through a web-based platform with intelligent market prediction capabilities.

### 1.3.2 Specific Objectives

1. To analyze the existing challenges faced by agricultural cooperatives and smallholder farmers in accessing market information and linking with buyers in Rwanda.

2. To design a multi-role web-based system architecture that supports farmers, cooperative managers, buyers, and system administrators with role-specific functionalities.

3. To develop an AI-based price prediction and demand forecasting module using statistical analysis of historical market data.

4. To implement a real-time market linkage module that enables buyers to browse cooperative inventories and place orders directly.

5. To evaluate the system's functionality, usability, and performance through testing and user feedback.

## 1.4 Research Questions

1. What are the key challenges faced by agricultural cooperatives and smallholder farmers in accessing market information and linking with buyers in Rwanda?

2. How can a multi-role web-based system be designed to effectively serve the needs of farmers, cooperative managers, buyers, and administrators?

3. How can AI-based statistical models be applied to historical agricultural market data to generate reliable price predictions and demand forecasts?

4. How can a digital marketplace be implemented to facilitate direct, transparent transactions between cooperatives and verified buyers?

5. To what extent does the developed system meet the functional and non-functional requirements of its intended users?

## 1.5 Scope of the Study

This study focuses on the design, development, and testing of the AgruKrwanda web-based system. The system covers the following scope:

Functional scope: The system includes user authentication and role management, farmer profile and harvest management, cooperative inventory and member management, buyer marketplace and order management, AI-powered price prediction and demand forecasting, real-time market price tracking, administrative dashboards, audit logging, and report generation in PDF and CSV formats.

User scope: The system serves four categories of users: system administrators, cooperative managers, registered farmers, and verified buyers.

Geographical scope: The system is designed for deployment in Rwanda, with geographic data covering all 30 districts of Rwanda organized by province, district, sector, cell, and village.

Technical scope: The system is developed as a PHP-based web application using the MVC (Model-View-Controller) architectural pattern, MySQL database, Bootstrap 5 for the frontend, and Chart.js for data visualization. The AI prediction module uses statistical linear regression on historical market price data.

Limitations: The study does not cover mobile application development, payment gateway integration, or real-time GPS tracking of deliveries. The AI prediction module relies on statistical methods rather than machine learning models requiring large training datasets.

## 1.6 Significance of the Study

To farmers: AgruKrwanda provides farmers with access to real-time market prices, AI-powered selling recommendations, and a transparent record of their harvest and sales history, enabling them to make informed decisions and negotiate better prices.

To cooperative managers: The system provides tools for managing members, recording harvests, managing inventory, processing orders, and generating reports, replacing manual and paper-based processes with efficient digital workflows.

To buyers: Verified buyers gain access to a structured marketplace where they can browse available crop inventories from cooperatives across Rwanda, compare prices, and place orders directly without intermediaries.

To administrators: System administrators gain a centralized dashboard with comprehensive analytics, user management, market price monitoring, AI prediction oversight, and audit logs for system governance.

To Rwanda's agricultural sector: The system contributes to Rwanda's digital transformation agenda in agriculture, supporting the goals of Vision 2050 and the National Strategy for Transformation (NST1) by leveraging technology to increase agricultural productivity and market efficiency.

To academia: This study contributes to the body of knowledge on the application of AI and web technologies in agricultural systems in developing countries, particularly in the East African context.

## 1.7 Organization of the Study

This report is organized into five chapters. Chapter One presents the introduction, including the background, problem statement, objectives, research questions, scope, and significance of the study. Chapter Two reviews related literature on agricultural market systems, cooperative management, AI in agriculture, and existing similar systems. Chapter Three describes the research methodology, system design, and development approach. Chapter Four presents the system implementation, testing results, and discussion of findings. Chapter Five provides conclusions and recommendations.

---

# CHAPTER TWO: LITERATURE REVIEW

## 2.1 Introduction

This chapter reviews existing literature relevant to the development of AgruKrwanda. It covers theoretical frameworks, empirical studies, and existing systems related to agricultural market information systems, cooperative management, AI applications in agriculture, and web-based market linkage platforms. The review identifies gaps in existing knowledge and systems that this study seeks to address.

## 2.2 Theoretical Framework

### 2.2.1 Information Asymmetry Theory

Information asymmetry, first formalized by Akerlof (1970) in his seminal paper "The Market for Lemons," describes situations where one party in a transaction has more or better information than the other. In agricultural markets, information asymmetry is pervasive: middlemen typically have better knowledge of market prices, buyer demand, and supply conditions than farmers, allowing them to exploit this advantage to purchase produce at below-market prices (Fafchamps & Minten, 2012).

AgruKrwanda directly addresses information asymmetry by providing all stakeholders — farmers, cooperatives, and buyers — with access to the same real-time market price data and AI-generated forecasts, leveling the information playing field.

### 2.2.2 Agricultural Value Chain Theory

The value chain concept, developed by Porter (1985) and applied to agriculture by Kaplinsky and Morris (2001), describes the full range of activities required to bring a product from production to the final consumer. In Rwanda's agricultural value chain, cooperatives occupy a critical intermediary position between smallholder farmers and markets. Strengthening cooperative capacity through digital tools can create value at multiple points in the chain, from post-harvest handling and storage to market linkage and price negotiation (World Bank, 2019).

### 2.2.3 Technology Acceptance Model (TAM)

Davis (1989) proposed the Technology Acceptance Model to explain how users come to accept and use technology. TAM posits that perceived usefulness and perceived ease of use are the primary determinants of technology adoption. In designing AgruKrwanda, these principles guided the development of an intuitive, role-specific user interface that minimizes complexity while maximizing the utility of features for each user category.

## 2.3 Agricultural Market Information Systems

Agricultural Market Information Systems (AMIS) are platforms that collect, process, and disseminate price and market data to agricultural stakeholders. Studies have consistently shown that access to market information significantly improves farmer bargaining power and income (Aker, 2011; Jensen, 2007).

In Rwanda, the Rwanda Agriculture and Animal Resources Development Board (RAB) maintains a market price monitoring system, but its reach to smallholder farmers remains limited due to poor internet connectivity in rural areas and lack of user-friendly interfaces (MINAGRI, 2020). Mobile-based AMIS such as M-Farm in Kenya and Esoko in Ghana have demonstrated the potential of digital platforms to deliver market information to farmers, but these systems focus primarily on price dissemination without integrating order management or AI prediction capabilities.

AgruKrwanda advances beyond simple price dissemination by integrating market prices with AI-powered predictions, inventory management, and direct buyer-cooperative transactions in a single platform.

## 2.4 Agricultural Cooperative Management Systems

Agricultural cooperatives are central to Rwanda's agricultural transformation strategy. The government has promoted cooperative formation through the Rwanda Cooperative Agency (RCA), and by 2022, Rwanda had over 8,000 registered cooperatives (RCA, 2022). However, most cooperatives lack digital management tools, relying on paper-based records for member management, harvest recording, inventory tracking, and financial reporting.

Studies by Verhofstadt and Maertens (2014) found that cooperative membership significantly increases smallholder farmer income in Rwanda, but the benefits are constrained by weak management capacity and poor market linkage. Digital cooperative management systems can address these constraints by automating administrative processes, improving data accuracy, and enabling evidence-based decision-making.

Existing cooperative management software such as CoopManager and FarmForce provide some of these capabilities but are not tailored to the Rwandan context, lack AI prediction features, and do not integrate direct market linkage with buyers.

## 2.5 Artificial Intelligence in Agriculture

The application of AI in agriculture has grown significantly in recent years, with applications ranging from crop disease detection using computer vision to yield prediction using machine learning and price forecasting using time series analysis (Liakos et al., 2018).

Price prediction: Several studies have applied machine learning models including ARIMA, LSTM neural networks, and Random Forest to predict agricultural commodity prices with varying degrees of accuracy (Choudhury et al., 2019; Ghosh et al., 2020). These models require large historical datasets for training, which may not be available in data-scarce environments like Rwanda.

Demand forecasting: AI-based demand forecasting helps cooperatives and farmers plan production quantities and timing to match market demand, reducing oversupply and post-harvest losses (FAO, 2021).

Statistical approaches: In contexts with limited data, statistical methods such as linear regression and moving averages provide reliable baseline predictions. AgruKrwanda's AI prediction module uses linear regression on historical price time series to predict future prices, classify demand as High, Medium, or Low based on price trend analysis, and generate actionable recommendations for farmers and cooperative managers.

## 2.6 Web-Based Agricultural Market Linkage Platforms

Several web-based platforms have been developed to link agricultural producers with buyers:

Twiga Foods (Kenya): A B2B platform connecting smallholder farmers with urban retailers through a mobile-based ordering system. Twiga has demonstrated significant reductions in post-harvest losses and improved farmer incomes (Twiga Foods, 2021).

Hello Tractor (Nigeria/Kenya): A platform connecting tractor owners with smallholder farmers for mechanization services, demonstrating the potential of digital platforms to improve agricultural productivity.

DigiFarm (Kenya): An integrated platform by Safaricom providing farmers with access to inputs, market information, and financial services through mobile phones.

AgroCenta (Ghana): A platform connecting smallholder farmers with buyers and providing last-mile logistics for agricultural produce.

While these platforms demonstrate the viability of digital agricultural market linkage, they share common limitations: they are not designed for the Rwandan cooperative structure, lack integrated AI prediction capabilities, do not provide comprehensive administrative oversight tools, and are not open-source or adaptable for local deployment.

## 2.7 MVC Architecture in Web Application Development

The Model-View-Controller (MVC) architectural pattern separates application logic into three interconnected components: the Model (data and business logic), the View (user interface), and the Controller (request handling and coordination). MVC is widely adopted in web application development for its benefits of code organization, maintainability, testability, and separation of concerns (Gamma et al., 1994).

AgruKrwanda is built on a custom PHP MVC framework, providing a lightweight, dependency-free architecture suitable for deployment on standard LAMP (Linux, Apache, MySQL, PHP) stacks commonly available in Rwanda's institutional environments.

## 2.8 Research Gap

The review of existing literature and systems reveals the following gaps that AgruKrwanda seeks to address:

1. No existing system integrates cooperative management, market linkage, AI prediction, and administrative oversight in a single platform tailored for Rwanda.

2. Existing AI agricultural systems require large datasets and complex infrastructure not available in Rwanda's smallholder farming context. AgruKrwanda uses statistical AI methods that work with limited historical data.

3. Existing market linkage platforms do not support the cooperative-centric structure of Rwanda's agricultural sector, where cooperatives act as intermediaries between farmers and buyers.

4. No existing system provides role-specific dashboards for all four stakeholder categories (administrators, cooperative managers, farmers, and buyers) with integrated reporting and audit capabilities.

## 2.9 Summary

This chapter reviewed theoretical frameworks and empirical literature on agricultural market information systems, cooperative management, AI in agriculture, and web-based market linkage platforms. The review established that while significant progress has been made globally, there remains a clear gap for an integrated, AI-powered, cooperative-centric agricultural market linkage system tailored for Rwanda. AgruKrwanda is designed to fill this gap.

---

# CHAPTER THREE: RESEARCH METHODOLOGY

## 3.1 Introduction

This chapter describes the research methodology, system design approach, development tools and technologies, database design, system architecture, and testing strategy used in developing AgruKrwanda. It explains the rationale for each methodological choice and provides sufficient detail for the study to be replicated.

## 3.2 Research Design

This study adopted a Design Science Research (DSR) methodology, which is appropriate for studies that aim to create and evaluate IT artifacts — in this case, a web-based information system (Hevner et al., 2004). DSR involves iterative cycles of design, development, and evaluation, with each cycle producing a refined artifact that better meets the defined requirements.

The study also incorporated elements of applied research, as it addresses a specific practical problem (poor market linkage and lack of AI tools for Rwandan agricultural cooperatives) through the development of a technological solution.

## 3.3 System Development Methodology

The system was developed using the Agile Software Development methodology, specifically an iterative and incremental approach. Agile was chosen because:

- Requirements for agricultural systems in developing countries are often not fully known at the outset and evolve through stakeholder engagement.
- Iterative development allows for early delivery of working software and continuous improvement based on feedback.
- The small development team benefits from Agile's lightweight process and flexibility.

Development was organized into the following phases:

Phase 1 - Requirements Analysis: Stakeholder interviews, literature review, requirements documentation
Phase 2 - System Design: Architecture design, database design, UI wireframing
Phase 3 - Implementation: Module-by-module development following MVC pattern
Phase 4 - Testing: Unit testing, integration testing, user acceptance testing
Phase 5 - Deployment: Local server deployment and documentation

## 3.4 Requirements Analysis

### 3.4.1 Functional Requirements

Administrator module:
- Manage users (create, edit, deactivate) with role assignment
- Manage cooperatives, farmers, and buyers
- Monitor and update market prices
- Manage crop categories and crop types
- View and manage orders system-wide
- Run AI predictions for any crop and district
- Generate reports (farmers, cooperatives, prices, orders, inventory, AI predictions)
- View audit logs and system settings

Cooperative Manager module:
- Manage cooperative members (farmers)
- Record and view harvest submissions from members
- Manage inventory (add stock, edit quantities and prices)
- Process orders (approve, reject, mark delivered)
- Run AI predictions for cooperative crops
- Generate cooperative-specific reports
- Manage production plans

Farmer module:
- Record harvests (crop, quantity, grade, season, date)
- View real-time market prices
- Receive AI-powered selling recommendations
- View sales history with order details
- Manage personal and farm profile

Buyer module:
- Browse cooperative marketplace with filters
- Place orders for available inventory
- Track order status and history
- View market prices and price history charts
- Manage business profile

### 3.4.2 Non-Functional Requirements

Security: Role-based access control, CSRF protection, password hashing (bcrypt), session security
Performance: Page load time under 3 seconds on standard connection
Usability: Responsive design supporting desktop and mobile browsers
Reliability: Input validation, error handling, database transaction management
Maintainability: MVC architecture, PSR-compliant code organization
Scalability: Modular design allowing addition of new modules

## 3.5 System Architecture

AgruKrwanda follows the Model-View-Controller (MVC) architectural pattern implemented in a custom PHP framework without external dependencies. The architecture consists of:

Presentation Layer (Views): PHP template files organized by role (admin/, farmer/, buyer/, cooperative/, public/, shared/) using Bootstrap 5 for responsive UI components and Chart.js for data visualization.

Application Layer (Controllers): Eight controllers handle HTTP requests and coordinate between models and views:
- HomeController: public pages
- AuthController: authentication (login, register, password reset)
- AdminController: administrator functions
- CooperativeController: cooperative manager functions
- FarmerController: farmer functions
- BuyerController: buyer functions
- ProfileController: shared profile management
- ApiController: AJAX endpoints for dynamic data

Data Layer (Models): Ten model classes encapsulate database operations:
- UserModel, FarmerModel, BuyerModel, CooperativeModel
- CropModel, HarvestModel, InventoryModel
- MarketPriceModel, OrderModel
- Model (base class with CRUD operations)

Services: Three service classes provide cross-cutting functionality:
- AIPredictionService: statistical price prediction and demand forecasting
- NotificationService: in-app notification management
- AuditLogger: system activity logging

Routing: A custom Router class maps HTTP method + URI patterns to controller methods, supporting both exact matches and parameterized routes.

## 3.6 Database Design

The database was designed using the Entity-Relationship (ER) model and implemented in MySQL with InnoDB engine for referential integrity through foreign key constraints. The database comprises 28 tables organized into the following logical groups:

Geography: districts, sectors, cells, villages — Rwanda's administrative hierarchy.
Users and Roles: roles, users, permissions, role_permissions — role-based access control.
Cooperatives: cooperatives, cooperative_members — cooperative profiles and farmer membership.
Farmers and Buyers: farmers, buyers — extended profiles with domain-specific attributes.
Crops: crop_categories, crops — crop taxonomy.
Production: harvests, inventories, warehouses, production_plans — produce flow tracking.
Market: market_prices, buyer_offers — price data and buyer demand signals.
Orders: orders, order_items, payments, deliveries — complete order lifecycle.
AI: ai_predictions — prediction results with confidence scores.
System: notifications, audit_logs, activity_logs, system_logs, settings, attachments, reports.

The database is normalized to Third Normal Form (3NF) to eliminate data redundancy and ensure data integrity.

## 3.7 AI Prediction Module Design

The AI prediction module (AIPredictionService) implements a statistical linear regression approach to price prediction.

Price Prediction: Given a time series of historical prices, the module fits a linear regression model P = alpha + beta*t and predicts the next period price as P(n+1) = alpha + beta*(n+1).

Demand Classification:
- Price change > +5%: High demand
- Price change < -5%: Low demand
- Otherwise: Medium demand

Confidence Score: Calculated as min(95, max(40, 3 * n_prices + 2 * n_sales)) where n_prices is the number of historical price records and n_sales is the number of completed sales records.

Recommendation Generation: Natural language recommendations combining demand forecast, predicted price direction, best buyer identification, and best selling period.

## 3.8 Security Design

Authentication: Passwords hashed using bcrypt. Sessions use strict mode with HttpOnly and SameSite=Strict cookie attributes.

Authorization: Role-based access control enforced at controller level through requireRole() and requireAuth() methods.

CSRF Protection: All POST requests include a CSRF token validated server-side using hash_equals() to prevent timing attacks.

Input Sanitization: All user inputs sanitized using htmlspecialchars(). Database queries use PDO prepared statements with parameterized queries, preventing SQL injection.

Audit Logging: All significant actions logged to audit_logs table with user ID, action type, affected record, old and new values, IP address, and timestamp.

## 3.9 Development Tools and Technologies

Backend language: PHP 8.2 — widely supported, LAMP stack compatibility
Database: MySQL 8.0 — relational integrity, JSON support, performance
Frontend framework: Bootstrap 5.3 — responsive design, extensive component library
Charts: Chart.js 4.4 — lightweight, flexible, no server-side dependencies
Icons: Bootstrap Icons 1.11 — consistent icon set
Web server: Apache 2.4 (LAMPP) — standard LAMP stack, .htaccess URL rewriting
Architecture: Custom PHP MVC — lightweight, no framework overhead, full control
Development environment: LAMPP on Linux

## 3.10 Testing Strategy

Unit Testing: Individual model methods and service functions tested with known inputs.
Integration Testing: Controller-model interactions tested by simulating HTTP requests.
Functional Testing: Each user story tested against acceptance criteria for all four roles.
Security Testing: CSRF protection, SQL injection, and role-based access tested.
Browser Compatibility: Tested on Google Chrome, Mozilla Firefox, and Microsoft Edge.
User Acceptance Testing (UAT): System demonstrated to representative users from each role category.

## 3.11 Summary

This chapter described the research design, system development methodology, requirements analysis, system architecture, database design, AI prediction module design, security design, development tools, and testing strategy for AgruKrwanda. The choices made reflect a deliberate balance between functionality, security, performance, and deployability in the Rwandan context.

---

## REFERENCES

Akerlof, G. A. (1970). The market for lemons: Quality uncertainty and the market mechanism. The Quarterly Journal of Economics, 84(3), 488–500.

Aker, J. C. (2011). Dial "A" for agriculture: A review of information and communication technologies for agricultural extension in developing countries. Agricultural Economics, 42(6), 631–647.

Choudhury, A., et al. (2019). Agricultural commodity price prediction using machine learning. International Journal of Computer Applications, 182(48), 1–6.

Davis, F. D. (1989). Perceived usefulness, perceived ease of use, and user acceptance of information technology. MIS Quarterly, 13(3), 319–340.

FAO. (2021). Artificial intelligence in agriculture. Food and Agriculture Organization of the United Nations.

Fafchamps, M., & Minten, B. (2012). Impact of SMS-based agricultural information on Indian farmers. The World Bank Economic Review, 26(3), 383–414.

Gamma, E., Helm, R., Johnson, R., & Vlissides, J. (1994). Design patterns: Elements of reusable object-oriented software. Addison-Wesley.

Ghosh, P., et al. (2020). Rice price prediction using LSTM and ARIMA models. Journal of Agricultural Informatics, 11(2), 1–12.

Hevner, A. R., March, S. T., Park, J., & Ram, S. (2004). Design science in information systems research. MIS Quarterly, 28(1), 75–105.

Jensen, R. (2007). The digital provide: Information (technology), market performance, and welfare in the South Indian fisheries sector. The Quarterly Journal of Economics, 122(3), 879–924.

Kaplinsky, R., & Morris, M. (2001). A handbook for value chain research. IDRC.

Liakos, K. G., et al. (2018). Machine learning in agriculture: A review. Sensors, 18(8), 2674.

MINAGRI. (2020). Strategic plan for the transformation of agriculture in Rwanda Phase IV (PSTA IV). Ministry of Agriculture and Animal Resources, Rwanda.

Porter, M. E. (1985). Competitive advantage: Creating and sustaining superior performance. Free Press.

RCA. (2022). Annual report on cooperatives in Rwanda. Rwanda Cooperative Agency.

Verhofstadt, E., & Maertens, M. (2014). Smallholder cooperatives and agricultural performance in Rwanda. Annals of Public and Cooperative Economics, 85(3), 421–442.

World Bank. (2019). Future of food: Harnessing digital technologies to improve food system outcomes. World Bank Group.
