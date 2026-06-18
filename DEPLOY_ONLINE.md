# Deploy HerCraft Hub Online (InfinityFree)

**Live URL:** https://hercrafthub.infinityfreeapp.com/

---

## Option A — FTP deploy (fastest)

### 1. Get your FTP password

1. Log in at https://dash.infinityfree.com/
2. Open your **hercrafthub** site
3. Go to **FTP Details** (or **FTP Accounts**)
4. Copy the FTP password shown there (may differ from your login password)

### 2. Configure deploy script

```powershell
copy C:\xampp\htdocs\hercrafthub\config\deploy.local.example.php C:\xampp\htdocs\hercrafthub\config\deploy.local.php
```

Edit `config\deploy.local.php` and paste your **FTP password** from vPanel:

```php
define('FTP_PASS', 'your-actual-ftp-password');
```

### 3. Upload

```powershell
cd C:\xampp\htdocs\hercrafthub
npm run deploy
```

### 4. Install database

Visit once:

```
https://hercrafthub.infinityfreeapp.com/install.php
```

Then **delete** `install.php` from the server (File Manager or FTP).

---

## Option B — Manual upload (no FTP)

### 1. Create ZIP

A ready-made zip is at:

```
C:\xampp\htdocs\hercrafthub-deploy.zip
```

Or recreate it:

```powershell
cd C:\xampp\htdocs\hercrafthub
# use robocopy + Compress-Archive (see project scripts)
```

### 2. Upload via File Manager

1. https://dash.infinityfree.com/ → your site → **File Manager**
2. Open folder **htdocs**
3. Delete **index2.html** (InfinityFree placeholder)
4. Upload and extract **hercrafthub-deploy.zip**
5. Ensure `config/db.php` is present (production DB credentials)

### 3. Set folder permissions

Make **uploads** and **uploads/avatars** writable (chmod 755 or 775 in File Manager).

### 4. Run installer

```
https://hercrafthub.infinityfreeapp.com/install.php
```

Delete `install.php` after success.

---

## Default admin login (after install)

| Field    | Value                      |
|----------|----------------------------|
| Email    | admin@hercrafthub.co.za    |
| Password | password                   |

Change this password immediately after first login.

---

## Verify deployment

| Page    | URL |
|---------|-----|
| Home    | https://hercrafthub.infinityfreeapp.com/ |
| Browse  | https://hercrafthub.infinityfreeapp.com/browse.php |
| Admin   | https://hercrafthub.infinityfreeapp.com/admin/ |

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| FTP `530 Login authentication failed` | Use FTP password from vPanel, not your forum/login password |
| Database connection error | Confirm `config/db.php` is uploaded and `db.local.php` is **not** on the server |
| Images won't upload | Set `uploads/` folder permissions to writable |
| Still shows InfinityFree welcome page | Delete `index2.html`; ensure `index.php` is in `htdocs` root |
