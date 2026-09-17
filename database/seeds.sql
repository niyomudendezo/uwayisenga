USE agrukrwanda;

-- ─── ROLES ───────────────────────────────────────────────────────────────────
INSERT INTO roles (name) VALUES ('admin'),('cooperative_manager'),('farmer'),('buyer');

-- ─── RWANDA DISTRICTS ────────────────────────────────────────────────────────
INSERT INTO districts (name, province) VALUES
('Gasabo','Kigali'),
('Kicukiro','Kigali'),
('Nyarugenge','Kigali'),
('Bugesera','Eastern'),
('Gatsibo','Eastern'),
('Kayonza','Eastern'),
('Kirehe','Eastern'),
('Ngoma','Eastern'),
('Nyagatare','Eastern'),
('Rwamagana','Eastern'),
('Gicumbi','Northern'),
('Gakenke','Northern'),
('Musanze','Northern'),
('Rulindo','Northern'),
('Burera','Northern'),
('Karongi','Western'),
('Ngororero','Western'),
('Nyabihu','Western'),
('Nyamasheke','Western'),
('Rubavu','Western'),
('Rusizi','Western'),
('Rutsiro','Western'),
('Gisagara','Southern'),
('Huye','Southern'),
('Kamonyi','Southern'),
('Muhanga','Southern'),
('Nyamagabe','Southern'),
('Nyanza','Southern'),
('Nyaruguru','Southern'),
('Ruhango','Southern');

-- ─── PERMISSIONS ─────────────────────────────────────────────────────────────
INSERT INTO permissions (name, module) VALUES
('manage_users','users'),('view_users','users'),
('manage_farmers','farmers'),('view_farmers','farmers'),
('manage_cooperatives','cooperatives'),('view_cooperatives','cooperatives'),
('manage_buyers','buyers'),('view_buyers','buyers'),
('manage_crops','crops'),('view_crops','crops'),
('manage_inventory','inventory'),('view_inventory','inventory'),
('manage_orders','orders'),('view_orders','orders'),('approve_orders','orders'),
('manage_prices','prices'),('view_prices','prices'),
('view_ai_predictions','ai'),('run_ai_predictions','ai'),
('manage_reports','reports'),('view_reports','reports'),
('manage_settings','settings'),('view_audit_logs','audit');

-- ─── CROP CATEGORIES ─────────────────────────────────────────────────────────
INSERT INTO crop_categories (name, description) VALUES
('Cereals','Grains and cereal crops'),
('Legumes','Beans, peas and leguminous crops'),
('Root Crops','Tubers and root vegetables'),
('Vegetables','Fresh vegetables'),
('Fruits','Fresh fruits'),
('Cash Crops','Export and cash crops');

-- ─── CROPS ───────────────────────────────────────────────────────────────────
INSERT INTO crops (category_id, name, variety, unit, description) VALUES
(1,'Maize','Hybrid H614D','kg','Yellow maize, drought-resistant variety'),
(1,'Rice','Jasmine','kg','Long-grain aromatic rice'),
(1,'Sorghum','Local','kg','Traditional sorghum variety'),
(1,'Wheat','Bread Wheat','kg','High-protein wheat'),
(2,'Beans','Climbing Beans','kg','High-yield climbing beans'),
(2,'Soybeans','Improved','kg','High-protein soybeans'),
(2,'Peas','Garden Peas','kg','Fresh garden peas'),
(3,'Cassava','Improved IITA','kg','Disease-resistant cassava'),
(3,'Sweet Potato','Orange-fleshed','kg','Vitamin A-rich sweet potato'),
(3,'Irish Potato','Kinigi','kg','Highland potato variety'),
(4,'Tomato','Hybrid','kg','Cherry and beef tomatoes'),
(4,'Cabbage','Green','kg','Fresh green cabbage'),
(4,'Onion','Red','kg','Red onion'),
(5,'Banana','Cooking Banana','bunch','Matoke cooking banana'),
(5,'Avocado','Hass','kg','Export-grade Hass avocado'),
(6,'Coffee','Arabica','kg','Specialty Arabica coffee'),
(6,'Tea','TRFK 6/8','kg','High-quality tea leaves');

-- ─── ADMIN USER (password: Admin@1234) ───────────────────────────────────────
INSERT INTO users (role_id, first_name, last_name, email, phone, password, status, email_verified_at, district_id)
VALUES (1,'System','Administrator','admin@agrukrwanda.rw','+250788000001',
        '$2y$12$knQkyPP5SG6JQ6s6uftrWemMkGJ76VuV5iYi4w4xiGAdrao5AriEe','active',NOW(),1);

-- ─── SAMPLE COOPERATIVE ──────────────────────────────────────────────────────
INSERT INTO users (role_id, first_name, last_name, email, phone, password, status, email_verified_at, district_id)
VALUES (2,'Jean','Mutabazi','manager@kigaligrains.rw','+250788000002',
        '$2y$12$knQkyPP5SG6JQ6s6uftrWemMkGJ76VuV5iYi4w4xiGAdrao5AriEe','active',NOW(),1);

INSERT INTO cooperatives (manager_id, name, registration_no, district_id, phone, email, description, status, established_at)
VALUES (2,'Kigali Grain Cooperative','COOP/KGL/2018/001',1,'+250788100001','info@kigaligrains.rw',
        'Leading grain cooperative in Kigali region','active','2018-03-15');

-- ─── SAMPLE FARMER ───────────────────────────────────────────────────────────
INSERT INTO users (role_id, first_name, last_name, email, phone, password, status, email_verified_at, district_id)
VALUES (3,'Amahoro','Uwimana','farmer@example.rw','+250788000003',
        '$2y$12$knQkyPP5SG6JQ6s6uftrWemMkGJ76VuV5iYi4w4xiGAdrao5AriEe','active',NOW(),4);

INSERT INTO farmers (user_id, national_id, cooperative_id, farm_name, farm_size, district_id)
VALUES (3,'1199780123456789',1,'Uwimana Farm',2.5,5);

-- ─── SAMPLE BUYER ────────────────────────────────────────────────────────────
INSERT INTO users (role_id, first_name, last_name, email, phone, password, status, email_verified_at, district_id)
VALUES (4,'Eric','Habimana','buyer@example.rw','+250788000004',
        '$2y$12$knQkyPP5SG6JQ6s6uftrWemMkGJ76VuV5iYi4w4xiGAdrao5AriEe','active',NOW(),1);

INSERT INTO buyers (user_id, company_name, business_type, district_id, verified, verified_at)
VALUES (4,'Habimana Trading Ltd','Grain Trader',1,1,NOW());

-- ─── SAMPLE WAREHOUSE ────────────────────────────────────────────────────────
INSERT INTO warehouses (cooperative_id, name, location, district_id, capacity, capacity_unit)
VALUES (1,'Kigali Main Warehouse','Gasabo, Kigali',1,50000,'kg');

-- ─── SAMPLE MARKET PRICES ────────────────────────────────────────────────────
INSERT INTO market_prices (crop_id, district_id, price, price_date, source, created_by) VALUES
(1,1,580,'2024-01-15','RAB Market Survey',1),(1,1,600,'2024-02-15','RAB Market Survey',1),
(1,1,590,'2024-03-15','RAB Market Survey',1),(1,1,620,'2024-04-15','RAB Market Survey',1),
(1,1,640,'2024-05-15','RAB Market Survey',1),(1,1,610,'2024-06-15','RAB Market Survey',1),
(5,1,450,'2024-01-15','RAB Market Survey',1),(5,1,470,'2024-02-15','RAB Market Survey',1),
(5,1,490,'2024-03-15','RAB Market Survey',1),(5,1,510,'2024-04-15','RAB Market Survey',1),
(10,1,350,'2024-01-15','RAB Market Survey',1),(10,1,380,'2024-02-15','RAB Market Survey',1),
(10,1,400,'2024-03-15','RAB Market Survey',1),(10,1,420,'2024-04-15','RAB Market Survey',1),
(8,1,200,'2024-01-15','RAB Market Survey',1),(8,1,220,'2024-02-15','RAB Market Survey',1),
(8,1,210,'2024-03-15','RAB Market Survey',1),(8,1,230,'2024-04-15','RAB Market Survey',1);

-- ─── SAMPLE HARVEST ──────────────────────────────────────────────────────────
INSERT INTO harvests (farmer_id, crop_id, cooperative_id, quantity, grade, harvest_date, season)
VALUES (1,1,1,1500,'A','2024-04-10','Season A 2024'),
       (1,5,1,800,'A','2024-04-15','Season A 2024'),
       (1,10,1,600,'B','2024-04-20','Season A 2024');

-- ─── SAMPLE INVENTORY ────────────────────────────────────────────────────────
INSERT INTO inventories (cooperative_id, warehouse_id, crop_id, harvest_id, qty_opening, qty_available, grade, buying_price, asking_price, harvest_date)
VALUES (1,1,1,1,1500,1500,'A',550,620,'2024-04-10'),
       (1,1,5,2,800,800,'A',420,490,'2024-04-15'),
       (1,1,10,3,600,600,'B',360,410,'2024-04-20');

INSERT INTO inventory_movements (inventory_id, movement_type, quantity, movement_date, reference_type, reference_id)
SELECT id, 'opening', qty_opening, CONCAT(harvest_date, ' 00:00:00'), 'inventory', id
FROM inventories WHERE qty_opening > 0;

-- ─── SAMPLE AI PREDICTIONS ───────────────────────────────────────────────────
INSERT INTO ai_predictions (crop_id, district_id, cooperative_id, predicted_demand, predicted_price, best_buyer_id, best_selling_period, estimated_revenue, confidence_score, suggested_qty, recommendation_text, prediction_date)
VALUES
(1,1,1,'High',640,1,'Next 7-14 days',960000,87.5,1500,'Maize demand is high in Kigali. Delay selling by 7 days to maximize profit. Best buyer: Habimana Trading Ltd.','2024-05-01'),
(5,1,1,'Medium',500,1,'Immediate',400000,72.3,800,'Bean prices are stable. Sell now to avoid storage costs.','2024-05-01'),
(10,1,1,'High',420,1,'Next 3-7 days',252000,81.0,600,'Irish potato demand rising due to hotel season. Sell within a week.','2024-05-01');

-- ─── SAMPLE ORDER ────────────────────────────────────────────────────────────
INSERT INTO orders (order_no, buyer_id, cooperative_id, total_amount, status, delivery_date, delivery_addr)
VALUES ('ORD-2024-0001',1,1,930000,'approved','2024-05-20','Kigali, Gasabo District');

INSERT INTO order_items (order_id, inventory_id, crop_id, quantity, unit, unit_price)
VALUES (1,1,1,1000,'kg',620),(1,2,5,500,'kg',490);

-- ─── SETTINGS ────────────────────────────────────────────────────────────────
INSERT INTO settings (key_name, value, group_name) VALUES
('site_name','AgruKrwanda','general'),
('site_email','info@agrukrwanda.rw','general'),
('site_phone','+250788000000','general'),
('currency','RWF','general'),
('items_per_page','15','general'),
('ai_service_url','http://localhost:8000','ai'),
('ai_enabled','1','ai'),
('smtp_host','smtp.gmail.com','email'),
('smtp_port','587','email'),
('low_stock_threshold','100','inventory');
