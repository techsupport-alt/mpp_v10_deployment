# MPP Website Deployment Guide

## 🚨 IMPORTANT: Security Setup for Production

### **Default Credentials Security Risk**
The database schema includes default admin credentials:
- **Email**: admin@mpp.org
- **Password**: admin123

**⚠️ These credentials will NOT change automatically when you deploy online!**

---

## 📋 Pre-Deployment Checklist

### **1. Database Configuration**
Create `/backend/config/.env` file:
```env
# Database Configuration
DB_TYPE=mysql
DB_HOST=your_database_host
DB_NAME=mpp_production_db
DB_USER=your_db_username
DB_PASS=your_secure_db_password
DB_PORT=3306

# Email Configuration (Optional)
SMTP_HOST=your_smtp_host
SMTP_PORT=587
SMTP_USER=noreply@yourdomain.com
SMTP_PASS=your_email_password

# Security
SITE_URL=https://yourdomain.com
ADMIN_EMAIL=admin@yourdomain.com
```

### **2. Database Setup**
```bash
# 1. Create production database
mysql -u root -p
CREATE DATABASE mpp_production_db;
GRANT ALL PRIVILEGES ON mpp_production_db.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;

# 2. Import schema
mysql -u your_db_user -p mpp_production_db < backend/database/schema.sql
```

### **3. File Permissions (Linux/Unix hosting)**
```bash
# Set proper permissions
chmod 755 backend/
chmod 644 backend/config/database.php
chmod 600 backend/config/.env
chmod 755 backend/api/
chmod 644 backend/api/*.php
chmod 755 backend/admin/
chmod 644 backend/admin/*.php
```

---

## 🔐 Secure Admin Setup Process

### **Step 1: Deploy Files**
Upload all backend files to your web server.

### **Step 2: Run Setup Script**
1. Navigate to: `https://yourdomain.com/backend/admin/setup.php`
2. Create your secure admin account
3. The setup script will:
   - Remove default admin@mpp.org account
   - Create your new secure admin user
   - Lock the setup script to prevent re-running

### **Step 3: Verify Security**
- Test login at: `https://yourdomain.com/backend/admin/login.php`
- Confirm default credentials no longer work
- Access dashboard at: `https://yourdomain.com/backend/admin/dashboard.php`

---

## 🛡️ Security Best Practices

### **1. Environment Variables**
Never commit sensitive data to version control:
```bash
# Add to .gitignore
backend/config/.env
backend/setup.lock
*.log
```

### **2. HTTPS Configuration**
Ensure your hosting provider has SSL/HTTPS enabled:
- All admin pages should be HTTPS only
- Database connections should use SSL
- Form submissions require HTTPS

### **3. Database Security**
```sql
-- Create dedicated database user with limited privileges
CREATE USER 'mpp_app'@'localhost' IDENTIFIED BY 'strong_random_password';
GRANT SELECT, INSERT, UPDATE ON mpp_production_db.* TO 'mpp_app'@'localhost';
GRANT DELETE ON mmp_production_db.admin_sessions TO 'mmp_app'@'localhost';
FLUSH PRIVILEGES;
```

### **4. Admin Security**
- Use strong passwords (12+ characters)
- Regular password changes
- Monitor admin login attempts
- Use secure email for admin account

---

## 📧 Email Configuration

### **Option 1: PHP mail() Function**
```php
// In prayer-signup.php and volunteer-signup.php
// Uncomment the mail() function calls
mail($to, $subject, $message, $headers);
```

### **Option 2: SMTP with PHPMailer**
```bash
# Install PHPMailer
composer require phpmailer/phpmailer
```

```php
// Example SMTP configuration
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host       = $_ENV['SMTP_HOST'];
$mail->SMTPAuth   = true;
$mail->Username   = $_ENV['SMTP_USER'];
$mail->Password   = $_ENV['SMTP_PASS'];
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;
```

---

## 🌐 Hosting Provider Setup

### **Shared Hosting (cPanel/Hostinger/Namecheap)**
1. Upload files via File Manager or FTP
2. Create database through hosting control panel
3. Update database credentials in `.env`
4. Ensure PHP 7.4+ is enabled

### **VPS/Dedicated Server**
```bash
# Install required packages
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-mbstring

# Configure Apache virtual host
sudo nano /etc/apache2/sites-available/mpp.conf

# Enable site
sudo a2ensite mpp.conf
sudo systemctl reload apache2
```

### **Cloud Hosting (AWS/DigitalOcean/Linode)**
- Use managed database services (RDS/Managed MySQL)
- Configure environment variables through hosting panel
- Set up automated backups
- Use CDN for static assets

---

## 📊 Testing Deployment

### **1. Frontend Testing**
- Visit your domain
- Test prayer signup form
- Test volunteer registration form
- Check responsive design on mobile

### **2. Backend Testing**
- Admin login functionality
- Dashboard analytics display
- Data export functionality
- Form submission processing

### **3. Security Testing**
- Attempt login with default credentials (should fail)
- Test SQL injection protection
- Verify HTTPS enforcement
- Check file permissions

---

## 🔄 Maintenance Tasks

### **Regular Tasks**
- **Weekly**: Check registration analytics
- **Monthly**: Export data backups
- **Quarterly**: Update admin passwords
- **Annually**: Review security settings

### **Database Backups**
```bash
# Create automated backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u backup_user -p mpp_production_db > /backups/mpp_backup_$DATE.sql
```

### **Log Monitoring**
- Monitor PHP error logs
- Track failed login attempts
- Review database performance
- Check SSL certificate expiry

---

## 🆘 Troubleshooting

### **Common Issues**

**Database Connection Failed**
```php
// Check database.php configuration
// Verify credentials in .env file
// Test database connectivity
```

**Admin Login Not Working**
```php
// Run setup.php again if needed
// Check password hash in database
// Verify session configuration
```

**Forms Not Submitting**
```php
// Check CORS headers
// Verify API endpoint URLs
// Test database insert permissions
```

### **Emergency Admin Reset**
If you lose admin access:
```sql
-- Direct database admin creation
INSERT INTO admin_users (id, email, password_hash, full_name, role) 
VALUES (UUID(), 'emergency@yourdomain.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emergency Admin', 'super_admin');
-- Password is: password123 (change immediately!)
```

---

## 📞 Support

For deployment assistance:
- Check hosting provider documentation
- Test with default credentials first (then run setup)
- Monitor error logs for specific issues
- Ensure all file paths are correct for your hosting structure

**Remember: Always run the admin setup script immediately after deployment to secure your admin access!**