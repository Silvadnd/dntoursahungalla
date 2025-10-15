# Railway Deployment Guide - DN Tours Ahungalla

## 🚀 Quick Deploy to Railway

### Step 1: Setup Railway Project

1. Go to https://railway.app
2. Sign in with GitHub
3. Click "New Project"
4. Choose "Deploy from GitHub repo"
5. Select: `Silvadnd/dntoursahungalla`

### Step 2: Add MySQL Database

1. In your Railway project dashboard
2. Click "+ New" button
3. Select "Database"
4. Choose "Add MySQL"
5. Wait for the database to provision

### Step 3: Configure Environment Variables

Railway automatically creates these variables when you add MySQL:
- `MYSQL_URL` (full connection string)
- `MYSQLHOST`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLPORT`

**IMPORTANT:** The code now automatically detects and uses Railway's MySQL variables!

#### Option A: Automatic (Recommended)
Railway's variables are automatically detected. No manual configuration needed!

#### Option B: Manual (if Option A doesn't work)
If you still see connection errors, manually set these:

1. Click on your web service
2. Go to "Variables" tab
3. Add these variables with reference to MySQL service:

```
DB_HOST = ${{MySQL.MYSQLHOST}}
DB_NAME = ${{MySQL.MYSQLDATABASE}}
DB_USER = ${{MySQL.MYSQLUSER}}
DB_PASSWORD = ${{MySQL.MYSQLPASSWORD}}
DB_PORT = ${{MySQL.MYSQLPORT}}
```

### Step 3.5: Test Database Connection

After deployment, visit:
```
https://your-app.railway.app/test_db_connection.php
```

This will show you:
- Environment variables status
- PHP extensions available
- Connection test results
- Debugging information

**IMPORTANT:** Delete `test_db_connection.php` after debugging for security!

### Step 4: Import Database Schema

#### Option A: Using Railway CLI
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link to your project
railway link

# Connect to MySQL
railway run mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < database_setup.sql
```

#### Option B: Using Database Client
1. Get your MySQL connection details from Railway
2. Connect using MySQL Workbench, phpMyAdmin, or any MySQL client
3. Run the contents of `database_setup.sql` file:
   - Create database `dn_tours` (or use Railway's database name)
   - Create tables: `reviews`, `gallery`, `admin_users`
   - Insert default admin user

#### Option C: Manual SQL Import
Connect to Railway MySQL and run these commands:

```sql
-- This creates the tables
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT NOT NULL,
    image_url VARCHAR(500),
    tour_photos TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(500) NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'general',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$tt6UNic8UUDyAlmzRkwFquP58dyPxcXdmX1..TCfkfwwSSOkvD5/K');
```

### Step 5: Deploy!

1. Railway will automatically deploy after you push to GitHub
2. Wait for the build to complete
3. Click "Generate Domain" to get your public URL
4. Your site will be live at: `https://your-app-name.railway.app`

## 🔧 Fixes Applied

### Fix 1: "Could not find driver" Error ✅
**Problem:** Railway didn't have PDO MySQL extension installed

**Solution:** Created `nixpacks.toml` with PHP extensions:
- php82
- php82Extensions.pdo
- php82Extensions.pdo_mysql
- php82Extensions.mysqli
- php82Extensions.mbstring

### Fix 2: 403 Forbidden Error ✅
**Problem:** Apache permissions blocking access

**Solution:** Updated `.htaccess` file:
- Added proper access rules
- Enabled directory index (index.php)
- Fixed file permission directives
- Allowed access to assets and PHP files

### Fix 3: Database Configuration ✅
**Problem:** Hardcoded localhost credentials

**Solution:** Updated `php/config.php`:
- Added environment variable support
- Railway-compatible connection string
- Better error messages
- UTF8MB4 charset support

## 📝 What Each File Does

| File | Purpose |
|------|---------|
| `nixpacks.toml` | Tells Railway to install PHP with MySQL extensions |
| `railway.json` | Railway deployment configuration |
| `.htaccess` | Apache server configuration (fixes 403 errors) |
| `php/config.php` | Database connection (uses environment variables) |
| `.gitignore` | Prevents sensitive files from being committed |
| `README.md` | Project documentation |

## ✅ Testing Your Deployment

After deployment, test these pages:

1. **Homepage:** `https://your-domain.railway.app/`
2. **Gallery:** `https://your-domain.railway.app/php/gallery.php`
3. **About:** `https://your-domain.railway.app/php/about.php`
4. **Admin:** `https://your-domain.railway.app/php/admin_login.php`

### If You See Errors:

**"Connection failed: could not find driver"**
- ✅ Fixed by `nixpacks.toml`
- Railway will install PDO MySQL on next deployment

**"Connection failed: No such file or directory"**
- ✅ Fixed by forcing TCP/IP connection (not Unix socket)
- Uses 127.0.0.1 instead of localhost for proper connection
- Auto-detects Railway environment

**"403 Forbidden"**
- ✅ Fixed by updated `.htaccess`
- Make sure Apache mod_rewrite is enabled

**"Unknown database"**
- ❌ Need to import `database_setup.sql` to Railway MySQL
- Follow Step 4 above

**"Connection failed: Access denied"**
- ❌ Check environment variables in Railway
- Make sure DB_HOST, DB_USER, DB_PASSWORD are correct

## 🔐 Security Notes

After deployment:

1. **Change admin password** (default: admin123)
2. **Update email credentials** in `php/email_config.php`
3. **Set up custom domain** (optional)
4. **Enable HTTPS** (Railway does this automatically)

## 📱 Share Your Live Site

Once deployed, your site will be at:
```
https://<your-project-name>.railway.app
```

You can also add a custom domain in Railway settings!

## 🆘 Need Help?

Check Railway logs:
1. Click on your service in Railway
2. Go to "Deployments" tab
3. Click on latest deployment
4. View logs to see any errors

Common issues:
- Database not connected → Check environment variables
- 500 errors → Check deployment logs
- Files not loading → Check .htaccess permissions

---

**Your site is now ready for Railway deployment! 🎉**

Every push to GitHub main branch will automatically redeploy your site.
