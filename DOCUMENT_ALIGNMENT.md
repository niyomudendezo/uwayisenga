# AgruKrwanda Dissertation Alignment

This implementation is aligned with the requirements in `Joselyne.docx` for the project **“AgruKrwanda: An AI-Powered Agricultural Cooperative and Market Linkage System for Rwanda.”**

## Requirements traceability

| Dissertation requirement | Implementation |
|---|---|
| Four roles | Administrator, cooperative manager, farmer, and buyer controllers and dashboards |
| Role-based access | Controller-level authentication and role enforcement with a dedicated HTTP 403 page |
| Farmer operations | Profile, harvest recording, market prices, AI recommendations, and sales history |
| Cooperative operations | Members, harvests, inventory, orders, production plans, AI predictions, and reports |
| Buyer market linkage | Available-inventory marketplace, offers, direct orders, status tracking, and prices |
| Administrator oversight | Users, cooperatives, farmers, buyers, crops, prices, inventory, orders, AI, reports, audit logs, and settings |
| AI price prediction | Local least-squares linear regression over historical crop prices |
| Demand forecast | High/Medium/Low classification based on price movement thresholds |
| Prediction confidence | Dynamic score based on available price and completed-sales records |
| Reporting | Printable PDF output and standards-compliant CSV downloads |
| Rwanda geography | Thirty districts and relational sector, cell, and village schema/API support |
| Security | Bcrypt, strict sessions, RBAC, CSRF, prepared PDO statements, escaped output, sensitive audit-value redaction, and browser security headers |
| Architecture | Custom PHP MVC application using controllers, models, views, services, and routing |

## Deliberate project boundaries

In accordance with the dissertation, the system does not implement a native mobile application, payment-gateway processing, or real-time GPS delivery tracking. The prediction engine remains statistical linear regression rather than claiming a large-data machine-learning model.

## Deployment data note

The database schema supports province/district/sector/cell/village relationships and the API exposes cascading geography endpoints. The official sector, cell, and village records must be imported from an authoritative Rwanda administrative dataset before production deployment; synthetic location records should not be presented as official data.
