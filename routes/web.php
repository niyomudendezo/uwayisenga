<?php
/** @var Router $router */

// ─── PUBLIC ──────────────────────────────────────────────────────────────────
$router->get('/',                    'HomeController',        'index');
$router->get('/about',               'HomeController',        'about');
$router->get('/contact',             'HomeController',        'contact');
$router->post('/contact',            'HomeController',        'sendContact');
$router->get('/market-prices',       'HomeController',        'marketPrices');
$router->get('/unauthorized',        'HomeController',        'unauthorized');

// ─── AUTH ─────────────────────────────────────────────────────────────────────
$router->get('/login',               'AuthController',        'loginForm');
$router->post('/login',              'AuthController',        'login');
$router->get('/register',            'AuthController',        'registerForm');
$router->post('/register',           'AuthController',        'register');
$router->get('/logout',              'AuthController',        'logout');
$router->get('/forgot-password',     'AuthController',        'forgotForm');
$router->post('/forgot-password',    'AuthController',        'forgotPassword');
$router->get('/reset-password/{token}', 'AuthController',    'resetForm');
$router->post('/reset-password',     'AuthController',        'resetPassword');

// ─── ADMIN ───────────────────────────────────────────────────────────────────
$router->get('/admin/dashboard',     'AdminController',       'dashboard');
$router->get('/admin/users',         'AdminController',       'users');
$router->get('/admin/users/create',  'AdminController',       'createUser');
$router->post('/admin/users/create', 'AdminController',       'storeUser');
$router->get('/admin/users/{id}/edit','AdminController',      'editUser');
$router->post('/admin/users/{id}/edit','AdminController',     'updateUser');
$router->post('/admin/users/{id}/delete','AdminController',   'deleteUser');
$router->get('/admin/cooperatives',  'AdminController',       'cooperatives');
$router->post('/admin/cooperatives/store','AdminController',    'storeCooperative');
$router->get('/admin/cooperatives/{id}/members','AdminController','cooperativeMembers');
$router->get('/admin/farmers',       'AdminController',       'farmers');
$router->get('/admin/buyers',        'AdminController',       'buyers');
$router->post('/admin/buyers/{id}/verify','AdminController',  'verifyBuyer');
$router->get('/admin/crops',         'AdminController',       'crops');
$router->post('/admin/crops/store',  'AdminController',       'storeCrop');
$router->post('/admin/crops/{id}/update','AdminController',   'updateCrop');
$router->get('/admin/market-prices', 'AdminController',       'marketPrices');
$router->post('/admin/market-prices/store','AdminController', 'storePrice');
$router->get('/admin/orders',        'AdminController',       'orders');
$router->get('/admin/reports',       'AdminController',       'reports');
$router->get('/admin/reports/farmers',      'AdminController', 'reportFarmers');
$router->get('/admin/reports/cooperatives', 'AdminController', 'reportCooperatives');
$router->get('/admin/reports/prices',       'AdminController', 'reportPrices');
$router->get('/admin/reports/orders',       'AdminController', 'reportOrders');
$router->get('/admin/reports/inventory',    'AdminController', 'reportInventory');
$router->get('/admin/reports/ai',           'AdminController', 'reportAi');
$router->get('/admin/audit-logs',    'AdminController',       'auditLogs');
$router->get('/admin/settings',      'AdminController',       'settings');
$router->post('/admin/settings',     'AdminController',       'saveSettings');
$router->get('/admin/inventory',     'AdminController',       'inventory');
$router->get('/admin/ai-predictions','AdminController',       'aiPredictions');
$router->post('/admin/ai-predictions/run','AdminController',  'runPrediction');

// ─── COOPERATIVE ─────────────────────────────────────────────────────────────
$router->get('/cooperative/dashboard',   'CooperativeController', 'dashboard');
$router->get('/cooperative/members',     'CooperativeController', 'members');
$router->get('/cooperative/inventory',   'CooperativeController', 'inventory');
$router->get('/cooperative/inventory/create','CooperativeController','createInventory');
$router->post('/cooperative/inventory/store','CooperativeController','storeInventory');
$router->get('/cooperative/inventory/{id}/edit','CooperativeController','editInventory');
$router->post('/cooperative/inventory/{id}/update','CooperativeController','updateInventory');
$router->post('/cooperative/inventory/{id}/stock-in','CooperativeController','stockIn');
$router->post('/cooperative/inventory/{id}/stock-out','CooperativeController','stockOut');
$router->get('/cooperative/harvests',    'CooperativeController', 'harvests');
$router->post('/cooperative/harvests/store','CooperativeController','storeHarvest');
$router->get('/cooperative/orders',      'CooperativeController', 'orders');
$router->get('/cooperative/orders/{id}', 'CooperativeController', 'orderDetail');
$router->post('/cooperative/orders/{id}/approve','CooperativeController','approveOrder');
$router->post('/cooperative/orders/{id}/reject', 'CooperativeController','rejectOrder');
$router->post('/cooperative/orders/{id}/deliver','CooperativeController','markDelivered');
$router->get('/cooperative/ai-predictions','CooperativeController','aiPredictions');
$router->post('/cooperative/ai-predictions/run','CooperativeController','runPrediction');
$router->get('/cooperative/reports',     'CooperativeController', 'reports');
$router->get('/cooperative/reports/members',   'CooperativeController', 'reportMembers');
$router->get('/cooperative/reports/harvests',  'CooperativeController', 'reportHarvests');
$router->get('/cooperative/reports/inventory', 'CooperativeController', 'reportInventory');
$router->get('/cooperative/reports/orders',    'CooperativeController', 'reportOrders');
$router->get('/cooperative/reports/ai',        'CooperativeController', 'reportAi');
$router->get('/cooperative/production-plans','CooperativeController','productionPlans');
$router->post('/cooperative/production-plans/store','CooperativeController','storePlan');

// ─── FARMER ──────────────────────────────────────────────────────────────────
$router->get('/farmer/dashboard',    'FarmerController',      'dashboard');
$router->get('/farmer/profile',      'FarmerController',      'profile');
$router->post('/farmer/profile',     'FarmerController',      'updateProfile');
$router->get('/farmer/harvests',     'FarmerController',      'harvests');
$router->post('/farmer/harvests/store','FarmerController',    'storeHarvest');
$router->get('/farmer/market-prices','FarmerController',      'marketPrices');
$router->get('/farmer/ai-recommendations','FarmerController', 'aiRecommendations');
$router->get('/farmer/sales-history','FarmerController',      'salesHistory');
$router->get('/farmer/sales-history/{id}','FarmerController',  'saleDetail');

// ─── BUYER ───────────────────────────────────────────────────────────────────
$router->get('/buyer/dashboard',     'BuyerController',       'dashboard');
$router->get('/buyer/marketplace',   'BuyerController',       'marketplace');
$router->get('/buyer/orders',        'BuyerController',       'orders');
$router->get('/buyer/orders/{id}',   'BuyerController',       'orderDetail');
$router->post('/buyer/orders/place', 'BuyerController',       'placeOrder');
$router->post('/buyer/orders/{id}/cancel','BuyerController',  'cancelOrder');
$router->get('/buyer/profile',       'BuyerController',       'profile');
$router->post('/buyer/profile',      'BuyerController',       'updateProfile');
$router->get('/buyer/market-prices', 'BuyerController',       'marketPrices');
$router->post('/buyer/offers/store', 'BuyerController',       'storeOffer');

// ─── SHARED API (AJAX) ───────────────────────────────────────────────────────
$router->get('/api/notifications',   'ApiController',         'notifications');
$router->post('/api/notifications/read','ApiController',      'markRead');
$router->get('/api/districts',       'ApiController',         'districts');
$router->get('/api/sectors/{districtId}','ApiController',     'sectors');
$router->get('/api/cells/{sectorId}',   'ApiController',      'cells');
$router->get('/api/villages/{cellId}',  'ApiController',      'villages');
$router->get('/api/crops',           'ApiController',         'crops');
$router->get('/api/price-chart/{cropId}','ApiController',     'priceChart');

// ─── PROFILE (shared) ────────────────────────────────────────────────────────
$router->get('/profile',             'ProfileController',     'index');
$router->post('/profile',            'ProfileController',     'update');
$router->post('/profile/password',   'ProfileController',     'changePassword');
