# HerCraft Hub – Project Documentation

HerCraft Hub is South Africa’s first custom C2C (Customer-to-Customer) marketplace platform tailored for women creators, artisans, and developers. It serves as a space where women can list and sell handmade crafts, technology-inspired crafts (Tech Crafts), digital assets, and beauty devices. The system is designed to run locally via XAMPP for development and features automated deployment options for staging and production hosting.

---

## 🛠️ Technology Stack & Dependencies

*   **Server-Side Core:** PHP 8.0+
*   **Database Management:** MySQL / MariaDB (v10.4+)
*   **Frontend Foundations:**
    *   HTML5 (Semantic elements)
    *   Vanilla CSS (Custom theme variables and modular styling)
    *   Bootstrap 5.3.0 (CSS and JS components loaded via CDN)
    *   Tabler Icons (Vector webfonts loaded via CDN)
*   **Local Server Environment:** XAMPP (Apache webserver + MariaDB engine)
*   **Build & Deployment Tooling:**
    *   Node.js & npm (for package management and deployment automation)
    *   FTP Deployment utilities (via npm packages)

---

## 📂 Project Directory Structure

Below is the directory mapping of the HerCraft Hub project codebase:

```text
hercrafthub/
│
├── admin/                         # Administrative Panel
│   ├── css/
│   │   └── admin.css              # Custom styling for the admin interface
│   ├── includes/
│   │   ├── admin_navbar.php       # Navigation bar for administrators
│   │   └── admin_sidebar.php      # Sidebar navigation links
│   ├── create_user.php            # Admin form to add new platform users
│   ├── edit_user.php              # Admin form to edit existing user profiles
│   ├── index.php                  # Admin Dashboard overview (stats & metrics)
│   ├── listings.php               # Moderation interface for product listings
│   ├── sales.php                  # Sales overview and log audit tool
│   └── users.php                  # User listing and status management dashboard
│
├── assets/                        # Design assets (CSS, Fonts, custom icons)
│
├── config/                        # Database and Configuration files
│   ├── db.example.php             # Database credentials template
│   ├── db.local.php               # Local DB credentials override (Git-ignored)
│   ├── db.php                     # Database connection initialization loader
│   ├── deploy.local.example.php   # Deployment FTP credentials template
│   └── deploy.local.php           # Active deployment FTP configuration (Git-ignored)
│
├── css/                           # Public stylesheets
│   ├── dashboard.css              # User Dashboard layout and styling
│   └── style.css                  # Core vintage typography and UI layout
│
├── database/                      # SQL Database structure & schema updates
│   ├── migrate_v2.sql             # SQL migration script for profile images & quantity
│   ├── migrate_v3.sql             # SQL migration script for detailed sales transactions
│   ├── schema.sql                 # Base structural SQL table definitions
│   └── schema_production.sql      # Production-hardened schema setup
│
├── images/                        # Platform logo and image resources
│   ├── 73b1e7ae-c52d-4bfe-843a-...# Placeholder or uploaded asset
│   └── logo.jpg.jpeg              # Brand identity logo
│
├── includes/                      # Public page header/footer components & PHP helpers
│   ├── auth.php                   # Authentication gatekeeper and privilege guards
│   ├── footer.php                 # Global responsive website footer
│   ├── helpers.php                # Reusable PHP utilities (formatting, image helpers, DB queries)
│   ├── logout_modal.php           # Confirmation modal for secure logout
│   ├── navbar.php                 # Top public site navigation bar
│   └── security.php               # Input sanitization and security utilities
│
├── js/                            # JavaScript behaviors
│   └── main.js                    # Global JavaScript scripts and modals
│
├── php/                           # Form actions and request processors
│   ├── checkout_action.php        # Process purchase transactions
│   ├── listing_manage.php         # Manage specific listing state changes
│   ├── login_action.php           # Process credentials validation
│   ├── logout.php                 # Secure session destroyer
│   ├── message_action.php         # Message database insertion action
│   ├── purchase_action.php        # Core backend transactional logger
│   ├── register_action.php        # Process registration inputs & validations
│   ├── sell_action.php            # Handle listing uploads and media saving
│   ├── update_listing_action.php  # Handles listing description/price/image updates
│   ├── update_profile.php         # Save changes made to user settings
│   ├── update_tracking_action.php # Update shipping tracking statuses
│   └── wishlist_action.php        # Handle adding/removing products from wishlists
│
├── uploads/                       # System upload web root (Git-ignored)
│   └── avatars/                   # Destination folder for user profile images
│
├── .gitignore                     # Git configuration rules for ignored files
├── DEPLOY_ONLINE.md               # Guide for staging/production server deployment
├── HOST.md                        # Instruction manual for running the project locally
├── package.json                   # NPM dependency scripts configuration
├── package-lock.json              # Fixed NPM package versions tree
├── [General Public Pages].php     # Frontend pages (about, index, browse, listing, sell, track_order, etc.)
└── install.php                    # Browser-based automated database setup script
```

---

## 🗄️ Database Architecture & Schema

The application database is named `hercrafthub`. It utilizes the `utf8mb4_unicode_ci` collation. Below is a breakdown of the relational database tables, their structures, and constraints:

### 1. Table: `users`
Stores all account details, credentials, and profile settings for buyers, sellers, and administrators.
*   **Columns:**
    *   `id` (INT, Primary Key, Auto Increment)
    *   `full_name` (VARCHAR(150), Not Null)
    *   `email` (VARCHAR(255), Unique, Not Null)
    *   `password` (VARCHAR(255), Not Null) - Stores bcrypt encrypted hashes.
    *   `role` (ENUM('buyer', 'seller', 'admin'), Default 'buyer')
    *   `is_active` (TINYINT(1), Default 1) - Soft-deactivation toggle.
    *   `is_verified` (TINYINT(1), Default 0) - Verification status check.
    *   `location` (VARCHAR(150), Nullable) - Seller region/city.
    *   `bio` (TEXT, Nullable) - Shop biography.
    *   `profile_image` (VARCHAR(255), Nullable) - Filename of user avatar.
    *   `created_at` (TIMESTAMP, Current Timestamp)
    *   `updated_at` (TIMESTAMP, Nullable, On Update Current Timestamp)
*   **Indexes:**
    *   `idx_users_role` (ON `role`)
    *   `idx_users_active` (ON `is_active`)
    *   `idx_users_email` (ON `email`)

### 2. Table: `categories`
Identifies the groupings for items listed on the platform.
*   **Columns:**
    *   `id` (INT, Primary Key, Auto Increment)
    *   `name` (VARCHAR(100), Unique, Not Null)
    *   `description` (VARCHAR(255), Nullable)
    *   `created_at` (TIMESTAMP, Current Timestamp)
*   **Default Records:**
    1.  `Tech Crafts` (Technology-inspired handmade goods)
    2.  `Handmade` (Traditional handcrafted items)
    3.  `Digital Art` (Digital downloads and templates)
    4.  `Accessories` (Jewellery, bags, and wearable crafts)
    5.  `Bundles` (Curated product bundles)
    6.  `Beauty Tech` (Beauty devices and tech accessories)

### 3. Table: `products`
Details for every item listed for sale on the marketplace.
*   **Columns:**
    *   `id` (INT, Primary Key, Auto Increment)
    *   `seller_id` (INT, Foreign Key referencing `users(id)`)
    *   `category_id` (INT, Foreign Key referencing `categories(id)`)
    *   `title` (VARCHAR(100), Not Null)
    *   `description` (TEXT, Not Null)
    *   `price` (DECIMAL(10, 2), Not Null)
    *   `image` (VARCHAR(255), Nullable) - File path of product photo.
    *   `condition_type` (ENUM('New', 'Like New', 'Good', 'Fair'), Default 'New')
    *   `location` (VARCHAR(150), Nullable)
    *   `is_active` (TINYINT(1), Default 1) - Soft visibility toggle.
    *   `is_sold` (TINYINT(1), Default 0) - Indicates if item is sold out.
    *   `quantity` (INT, Default 1) - Stock inventory.
    *   `created_at` (TIMESTAMP, Current Timestamp)
    *   `updated_at` (TIMESTAMP, Nullable, On Update Current Timestamp)
*   **Foreign Keys:**
    *   `fk_products_seller`: References `users(id)` (ON DELETE CASCADE ON UPDATE CASCADE)
    *   `fk_products_category`: References `categories(id)` (ON DELETE RESTRICT ON UPDATE CASCADE)
*   **Indexes:**
    *   `idx_products_seller` (ON `seller_id`)
    *   `idx_products_category` (ON `category_id`)
    *   `idx_products_active` (ON `is_active`)
    *   `idx_products_sold` (ON `is_sold`)
    *   `idx_products_quantity` (ON `quantity`)

### 4. Table: `wishlists`
A relational bridge table connecting buyers to their saved listings.
*   **Columns:**
    *   `id` (INT, Primary Key, Auto Increment)
    *   `user_id` (INT, Foreign Key referencing `users(id)`)
    *   `product_id` (INT, Foreign Key referencing `products(id)`)
    *   `created_at` (TIMESTAMP, Current Timestamp)
*   **Constraints:**
    *   `uq_wishlist` (UNIQUE composite index on `user_id`, `product_id`)
    *   `fk_wishlist_user` (ON DELETE CASCADE)
    *   `fk_wishlist_product` (ON DELETE CASCADE)
*   **Indexes:**
    *   `idx_wishlists_user` (ON `user_id`)

### 5. Table: `sales`
Logs all checkout transactions, shipping logs, tracking data, and optional charity donations.
*   **Columns:**
    *   `id` (INT, Primary Key, Auto Increment)
    *   `product_id` (INT, Foreign Key referencing `products(id)`)
    *   `seller_id` (INT, Foreign Key referencing `users(id)`)
    *   `buyer_id` (INT, Foreign Key referencing `users(id)`)
    *   `quantity` (INT, Default 1)
    *   `unit_price` (DECIMAL(10,2), Not Null)
    *   `item_total` (DECIMAL(10,2), Not Null)
    *   `delivery_address` (VARCHAR(500), Not Null)
    *   `delivery_fee` (DECIMAL(10,2), Default 0.00)
    *   `charity_donation` (DECIMAL(10,2), Default 0.00)
    *   `total_amount` (DECIMAL(10,2), Not Null) - Calculation of `item_total + delivery_fee + charity_donation`.
    *   `tracking_status` (ENUM('Processing', 'Shipped', 'In Transit', 'Delivered'), Default 'Processing')
    *   `created_at` (TIMESTAMP, Current Timestamp)
*   **Foreign Keys:**
    *   `fk_sales_product` (ON DELETE CASCADE)
    *   `fk_sales_seller` (ON DELETE CASCADE)
    *   `fk_sales_buyer` (ON DELETE CASCADE)
*   **Indexes:**
    *   `idx_sales_seller` (ON `seller_id`)
    *   `idx_sales_buyer` (ON `buyer_id`)
    *   `idx_sales_product` (ON `product_id`)

### 6. Table: `messages`
Stores direct message histories contextually tied to individual products.
*   **Columns:**
    *   `id` (INT, Primary Key, Auto Increment)
    *   `sender_id` (INT, Foreign Key referencing `users(id)`)
    *   `receiver_id` (INT, Foreign Key referencing `users(id)`)
    *   `product_id` (INT, Nullable, Foreign Key referencing `products(id)`)
    *   `body` (TEXT, Not Null)
    *   `is_read` (TINYINT(1), Default 0)
    *   `created_at` (TIMESTAMP, Current Timestamp)
*   **Foreign Keys:**
    *   `fk_messages_sender` (ON DELETE CASCADE)
    *   `fk_messages_receiver` (ON DELETE CASCADE)
    *   `fk_messages_product` (ON DELETE SET NULL)
*   **Indexes:**
    *   `idx_messages_sender` (ON `sender_id`)
    *   `idx_messages_receiver` (ON `receiver_id`)
    *   `idx_messages_product` (ON `product_id`)

---

## ⚙️ Configuration & Environment

The application configuration relies on a dual-environment configuration mechanism:
1.  **Production Configuration:** Located inside [config/db.php](file:///c:/xampp/htdocs/hercrafthub/config/db.php). This file runs configuration fallbacks and checks for local overrides.
2.  **Local Configuration:** Inside [config/db.local.php](file:///c:/xampp/htdocs/hercrafthub/config/db.local.php) (created from the template [config/db.example.php](file:///c:/xampp/htdocs/hercrafthub/config/db.example.php)). If this file is detected, it is loaded automatically, overriding production settings to enable offline development with XAMPP database values:
    ```php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'hercrafthub');
    ```

### Authentication Architecture
*   Authentication states are handled using standard secure PHP sessions via `session_start()`.
*   [includes/auth.php](file:///c:/xampp/htdocs/hercrafthub/includes/auth.php) contains helper functions to restrict page access:
    *   `require_login()`: Blocks unauthenticated guests and redirects them to the login screen.
    *   `require_role($role)`: Restricts page access to specific user profiles.
    *   `require_seller()`: Restricts access to sellers.
    *   `require_admin()`: Restricts access to platform administrators.

---

## 🚀 Setup & Execution Guide (Local Server)

For complete environment configuration steps, refer to [HOST.md](file:///c:/xampp/htdocs/hercrafthub/HOST.md).

1.  **XAMPP Setup:** Install XAMPP on Windows. Move the `hercrafthub` codebase into `C:\xampp\htdocs\hercrafthub`.
2.  **Database Migration:** Start Apache and MySQL from the XAMPP Control Panel. Create the local database and seed schemas using either phpMyAdmin or the mysql CLI:
    ```powershell
    C:\xampp\mysql\bin\mysql.exe -u root < C:\xampp\htdocs\hercrafthub\database\schema.sql
    ```
3.  **Local Configuration:** Copy the configuration file:
    ```powershell
    copy C:\xampp\htdocs\hercrafthub\config\db.example.php C:\xampp\htdocs\hercrafthub\config\db.local.php
    ```
4.  **Verification:** Ensure write permissions are granted to `uploads/` and `uploads/avatars/`. Visit `http://localhost/hercrafthub/` in your browser.

---

## 🌐 Deployment to Live Hosting (Production)

Staging and production deployment steps are outlined in detail within [DEPLOY_ONLINE.md](file:///c:/xampp/htdocs/hercrafthub/DEPLOY_ONLINE.md).

### FTP Script Deployment
1.  Copy `config/deploy.local.example.php` to `config/deploy.local.php`.
2.  Set your FTP password and host address.
3.  Run the terminal script:
    ```powershell
    npm run deploy
    ```
4.  Run the browser installer: `https://[your-domain]/install.php` to deploy schemas and default configurations, and then delete the file from the remote filesystem.
