MASTER DATA CATEGORY (MDC) + MASTER DATA ITEM (MDI)
==================================================

Target branch used as the implementation reference:
feat/myanmar-address-blade-addresses

IMPORTANT
---------
This package adds new master_data_categories and master_data_items tables.
It does NOT edit, rename, delete, or migrate the existing common_tables table.
It does NOT modify CommonTables, CommonTable, CommonTableRepository,
CommonTableInterface, CommonTablesRequest, or the existing common migration.

INSTALL
-------
1. Extract this ZIP into the Laravel project root (the folder containing artisan).
2. Open Git Bash / terminal in that project root.
3. Run:

   python install_master_data.py

The installer creates backups of existing files before modifying them:
   *.master-data-backup

It updates only:
   app/Providers/AppServiceProvider.php
   app/Providers/AuthServiceProvider.php
   config/eager.php
   routes/api.php

New CRUD classes are extracted into their exact app/ and database/ paths.

DATABASE
--------
Run from the project root (or your Docker app container):

   php artisan migrate

For Docker Compose from the host project folder, use the service name used by
your project. In the setup previously used for this project:

   docker compose exec app php artisan migrate

The two migrations create:
   master_data_categories
   master_data_items

Uniqueness:
- master_data_categories.key is globally unique.
- (master_data_category_id, master_data_items.key) is unique.
- Deleting a category that still has items is restricted. Delete its items first.

API ROUTES
----------
All routes require auth:api and role:super-admin:

GET     /api/v1/master-data-categories
POST    /api/v1/master-data-categories
GET     /api/v1/master-data-categories/{master_data_category}
PUT     /api/v1/master-data-categories/{master_data_category}
PATCH   /api/v1/master-data-categories/{master_data_category}
DELETE  /api/v1/master-data-categories/{master_data_category}

GET     /api/v1/master-data-items
POST    /api/v1/master-data-items
GET     /api/v1/master-data-items/{master_data_item}
PUT     /api/v1/master-data-items/{master_data_item}
PATCH   /api/v1/master-data-items/{master_data_item}
DELETE  /api/v1/master-data-items/{master_data_item}

EXAMPLE CATEGORY BODY
---------------------
{
  "key": "order-status",
  "name": "Order Status",
  "description": "General order statuses",
  "sort_order": 1,
  "is_active": true
}

EXAMPLE ITEM BODY
-----------------
{
  "master_data_category_id": 1,
  "key": "pending",
  "value": "Pending",
  "label": "Pending",
  "sort_order": 1,
  "is_active": true
}

VALIDATION / AUTHORIZATION
--------------------------
The Form Requests also authorize only super-admin users. Policies enforce the
same restriction for read, update, and delete operations. API route middleware
adds another server-side role check.

VERIFY
------
   php artisan optimize:clear
   php artisan route:list --path=api/v1/master-data
   php artisan migrate:status

With Docker Compose:
   docker compose exec app php artisan optimize:clear
   docker compose exec app php artisan route:list --path=api/v1/master-data
   docker compose exec app php artisan migrate:status

No frontend screens, seeders, permissions, or common-table removal are included.
