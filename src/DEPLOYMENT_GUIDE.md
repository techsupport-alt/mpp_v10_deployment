# Beginner's Guide to Deploying PHP-MySQL on Hostinger

## 1️⃣ Preparing Your PHP Application

### Folder Structure Setup
```
public_html/          # Main folder Hostinger serves
├── assets/           # CSS, JS, images
├── src/              # PHP source files
├── config/           # Configuration files
└── .htaccess         # Important security file
```

Why this matters: Hostinger expects your public files in `public_html`. This structure keeps sensitive files secure.

## 2️⃣ Uploading Files to Hostinger

1. **Compress your files**:
   - Right-click your project folder → "Compress" (or use zip command)
   - Exclude: node_modules/, .git/, any large backup files

2. **Upload to Hostinger**:
   - Login to Hostinger → File Manager
   - Navigate to `public_html` folder
   - Click "Upload" → Select your zip file
   - Right-click the zip → "Extract"

## 3️⃣ Creating MySQL Database

1. In Hostinger control panel:
   - Go to "Databases" → "MySQL Databases"
   - Click "Create Database"
   
2. Note these details (Hostinger will show them):
   - Database name (starts with `u123_`)
   - Username (same format)
   - Password (create a strong one)
   - Host (usually `localhost`)

Why this matters: Your PHP app needs these details to connect to the database.

## 4️⃣ Updating Database Connection

Edit `config/database.php` (or where your DB settings are):

```php
<?php
$db_host = 'localhost'; // From Hostinger
$db_name = 'u123_yourdb'; // Your database name
$db_user = 'u123_youruser'; // Your username
$db_pass = 'your_secure_password'; // Your password

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
```

## 5️⃣ Setting File Permissions

In Hostinger File Manager:

1. Right-click each folder → "Change Permissions":
   - `public_html/` → 755
   - `config/` → 750
   - `admin/` → 755

2. For files:
   - PHP files → 644
   - Config files → 640

Why: Prevents hackers from modifying your files while allowing the web server to read them.

## 6️⃣ Configuring .htaccess

Create/update `public_html/.htaccess`:

```apache
# Basic security
Options -Indexes # Stops directory listing
ServerSignature Off # Hides server info

# PHP settings
php_value upload_max_filesize 10M
php_value post_max_size 12M

# URL routing (if using pretty URLs)
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [L,QSA]
```

## 7️⃣ Testing Your Application

1. **Frontend Tests**:
   - Visit your domain (https://yourdomain.com)
   - Test all forms (they should submit without errors)
   - Check images/CSS loading

2. **Backend Tests**:
   - Try admin login (https://yourdomain.com/admin)
   - Test database features (add/edit/delete records)

3. **Security Checks**:
   - Verify the padlock icon (HTTPS works)
   - Try accessing `config/database.php` directly (should give 403 error)

## 🆘 Troubleshooting Common Issues

1. **White screen?**
   - Check for PHP errors in Hostinger's "Error Log"
   - Verify PHP version (needs 7.4+)

2. **Database connection failed?**
   - Double-check credentials
   - Make sure MySQL is enabled in Hostinger

3. **403 Forbidden errors?**
   - Adjust file permissions
   - Check .htaccess rules

## 🔄 Keeping Your Site Updated

1. **Before making changes**:
   - Backup via Hostinger's "Backups" tool
   - Download your database from phpMyAdmin

2. **When updating files**:
   - Upload changed files only
   - Clear browser cache after updates

Remember: Hostinger's 24/7 chat support can help with hosting issues!
