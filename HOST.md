# HerCraft Hub – Local Hosting Guide (Windows + XAMPP)

This guide walks you through running HerCraft Hub on your machine using XAMPP.

---

## Prerequisites

1. **XAMPP** installed (includes Apache, MySQL/MariaDB, and PHP)  
   Download: https://www.apachefriends.org/

2. **PHP 8.0+** (included with XAMPP)

3. This project folder placed inside your XAMPP web root:

```
C:\xampp\htdocs\hercrafthub
```

---

## Step 1 – Start XAMPP Services

Open **XAMPP Control Panel** and start:

- **Apache**
- **MySQL**

Or from PowerShell (run as Administrator if needed):

```powershell
cd C:\xampp
.\apache_start.bat
.\mysql_start.bat
```

---

## Step 2 – Create the Database

### Option A: phpMyAdmin (easiest)

1. Open your browser and go to: http://localhost/phpmyadmin
2. Click **Import**
3. Choose `database\schema.sql` from this project
4. Click **Go**

If you already have an older database, also import:

```
database\migrate_v2.sql
```

### Option B: MySQL command line

```powershell
cd C:\xampp\mysql\bin
.\mysql.exe -u root -p < C:\xampp\htdocs\hercrafthub\database\schema.sql
```

If your root user has no password (default XAMPP), use:

```powershell
.\mysql.exe -u root < C:\xampp\htdocs\hercrafthub\database\schema.sql
```

For existing databases, run the migration too:

```powershell
.\mysql.exe -u root < C:\xampp\htdocs\hercrafthub\database\migrate_v2.sql
```

---

## Step 3 – Configure Database Connection

The app uses `config\db.local.php` for local settings (this file overrides production credentials).

1. Copy the example file if you do not already have `db.local.php`:

```powershell
copy C:\xampp\htdocs\hercrafthub\config\db.example.php C:\xampp\htdocs\hercrafthub\config\db.local.php
```

2. Edit `config\db.local.php` and confirm these values match your XAMPP setup:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // empty by default on XAMPP
define('DB_NAME', 'hercrafthub');
```

> **Note:** `config\db.local.php` is git-ignored so your local credentials stay private.

---

## Step 4 – Ensure Upload Folders Exist

Product images and avatars are stored in `uploads\`. The folders should already exist, but create them if needed:

```powershell
mkdir C:\xampp\htdocs\hercrafthub\uploads\avatars -Force
```

---

## Step 5 – Open the Site

Visit:

```
http://localhost/hercrafthub/
```

Or the home page directly:

```
http://localhost/hercrafthub/index.php
```

---

## Step 6 – Create Your First Account

1. Go to **Register**: http://localhost/hercrafthub/register.php
2. Create a **buyer** or **seller** account
3. Log in at: http://localhost/hercrafthub/login.php

To test selling:

1. Register as a **seller**, or update your role in phpMyAdmin
2. Complete your profile (add a location): http://localhost/hercrafthub/profile.php
3. Post a listing: http://localhost/hercrafthub/sell.php

---

## Useful URLs

| Page        | URL                                              |
|-------------|--------------------------------------------------|
| Home        | http://localhost/hercrafthub/                    |
| Browse      | http://localhost/hercrafthub/browse.php          |
| Login       | http://localhost/hercrafthub/login.php           |
| Register    | http://localhost/hercrafthub/register.php        |
| Dashboard   | http://localhost/hercrafthub/dashboard.php       |
| Profile     | http://localhost/hercrafthub/profile.php         |
| Sell        | http://localhost/hercrafthub/sell.php            |
| phpMyAdmin  | http://localhost/phpmyadmin                      |

---

## Stopping the Server

In XAMPP Control Panel, click **Stop** next to Apache and MySQL.

Or from PowerShell:

```powershell
cd C:\xampp
.\apache_stop.bat
.\mysql_stop.bat
```

---

## Troubleshooting

### "The service is temporarily unavailable"

- MySQL is not running — start it in XAMPP Control Panel
- Database `hercrafthub` does not exist — re-run Step 2
- Wrong credentials in `config\db.local.php` — check username/password

### Blank page or PHP errors

- Enable error display in `C:\xampp\php\php.ini`:
  ```
  display_errors = On
  error_reporting = E_ALL
  ```
- Restart Apache after changing `php.ini`

### Images not uploading

- Confirm `uploads\` and `uploads\avatars\` folders exist
- Check `php.ini` upload limits:
  ```
  upload_max_filesize = 2M
  post_max_size = 8M
  ```

### Page not found (404)

- Confirm the project is in `C:\xampp\htdocs\hercrafthub`
- Use `http://localhost/hercrafthub/` (not `http://localhost/`)

### Port 80 already in use

- Another app (IIS, Skype, etc.) may be using port 80
- In XAMPP, change Apache port to **8080**, then use:
  ```
  http://localhost:8080/hercrafthub/
  ```

---

## Quick Start (all commands)

Run these in order for a fresh setup:

```powershell
# 1. Start services (via XAMPP Control Panel, or):
cd C:\xampp
.\apache_start.bat
.\mysql_start.bat

# 2. Create database
cd C:\xampp\mysql\bin
.\mysql.exe -u root < C:\xampp\htdocs\hercrafthub\database\schema.sql

# 3. Copy local DB config (skip if db.local.php already exists)
copy C:\xampp\htdocs\hercrafthub\config\db.example.php C:\xampp\htdocs\hercrafthub\config\db.local.php

# 4. Ensure upload folders exist
mkdir C:\xampp\htdocs\hercrafthub\uploads\avatars -Force

# 5. Open in browser
start http://localhost/hercrafthub/
```

---

Built for local development on Windows with XAMPP. For production deployment, use a proper web host with HTTPS and secure database credentials.
