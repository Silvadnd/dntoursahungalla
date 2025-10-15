# 🔧 Railway Database Connection Fix

## ✅ The Problem You Had

**Error:** `Connection failed: SQLSTATE[HY000] [2002] No such file or directory`

**What it means:** PHP was trying to connect to MySQL using a Unix socket file (localhost) instead of a TCP/IP connection, which doesn't work on Railway's hosting environment.

## ✅ What Was Fixed

### 1. **Force TCP/IP Connection**
- Changed from `localhost` to `127.0.0.1` for local development
- On Railway, uses the proper hostname from environment variables
- Prevents PHP from looking for Unix socket files

### 2. **Auto-detect Railway Environment**
- Code now automatically detects Railway's environment variables
- Supports both `MYSQL_URL` and individual `MYSQL*` variables
- Falls back to custom `DB_*` variables if needed

### 3. **Better Error Messages**
- Shows detailed debug info in development
- Generic messages in production (for security)
- Helps identify exactly what's wrong

### 4. **Connection Test Tool**
- Added `test_db_connection.php` to diagnose issues
- Shows all environment variables (passwords hidden)
- Tests multiple connection methods

## 🚀 How to Deploy the Fix

### Automatic (Railway will do this):

1. **Railway detects the push** from GitHub
2. **Reads `nixpacks.toml`** and installs PHP with MySQL extensions
3. **Deploys automatically** with the new config
4. **Your site should now work!**

### To Test Your Deployment:

1. **Visit your Railway domain:**
   ```
   https://your-app-name.railway.app
   ```

2. **Run the database test:**
   ```
   https://your-app-name.railway.app/test_db_connection.php
   ```
   
   You should see:
   ```json
   {
     "overall_status": "DATABASE CONNECTION OK",
     "connection_attempts": [
       {
         "method": "MYSQL_URL",
         "status": "SUCCESS"
       }
     ]
   }
   ```

3. **Test the gallery page:**
   ```
   https://your-app-name.railway.app/php/gallery.php
   ```
   
   Should load without errors!

4. **DELETE the test file** (for security):
   - In Railway dashboard or via commit
   - Delete: `test_db_connection.php`

## 📋 Railway Checklist

Make sure these are done in your Railway project:

- ✅ **MySQL Database Added** (New > Database > MySQL)
- ✅ **Environment Variables Set** (Automatic when MySQL added)
- ✅ **Database Schema Imported** (Run `database_setup.sql`)
- ✅ **Latest Code Deployed** (Automatic from GitHub)
- ✅ **Domain Generated** (Settings > Generate Domain)

## 🔍 If You Still See Errors

### Error: "could not find driver"
**Solution:** Railway is still deploying. Wait 2-3 minutes.

### Error: "No such file or directory"
**Solution:** Check Railway logs:
1. Click on your service
2. Go to "Deployments"
3. Check build logs for errors

### Error: "Unknown database"
**Solution:** Import the database schema:
```sql
-- Connect to Railway MySQL and run:
CREATE TABLE IF NOT EXISTS gallery (...);
CREATE TABLE IF NOT EXISTS reviews (...);
CREATE TABLE IF NOT EXISTS admin_users (...);
```

### Error: "Access denied for user"
**Solution:** Environment variables are wrong:
1. Check Railway Variables tab
2. Make sure `MYSQLUSER` and `MYSQLPASSWORD` are set
3. Or manually set `DB_USER` and `DB_PASSWORD`

## 📱 What Changed in the Code

### `php/config.php`
```php
// OLD (didn't work on Railway):
$host = 'localhost';  // ❌ Tries to use Unix socket

// NEW (works on Railway):
$host = getenv('MYSQLHOST') ?: '127.0.0.1';  // ✅ Uses TCP/IP
// Also parses MYSQL_URL automatically
```

### `nixpacks.toml`
```toml
# Ensures PDO MySQL extension is installed
nixPkgs = [
    "php82",
    "php82Extensions.pdo",
    "php82Extensions.pdo_mysql",
    "php82Extensions.mysqli"
]
```

## 🎉 Expected Result

After Railway redeploys:
- ✅ Homepage loads: `https://your-app.railway.app/`
- ✅ Gallery loads: `https://your-app.railway.app/php/gallery.php`
- ✅ All pages work without database errors
- ✅ Admin panel accessible: `https://your-app.railway.app/php/admin_login.php`

## 🔐 Security Reminder

After confirming everything works:

1. **Delete test file:**
   ```bash
   rm test_db_connection.php
   git commit -m "Remove database test file"
   git push
   ```

2. **Change admin password** (default: admin123)

3. **Never commit** `php/config.php` with real credentials
   (Already protected by `.gitignore`)

## 💡 Pro Tips

**Local Development:**
- Use `127.0.0.1` instead of `localhost` in config
- Or install and configure XAMPP to use TCP/IP

**Railway Deployment:**
- Let Railway manage environment variables automatically
- Don't hardcode database credentials
- Check deployment logs if issues persist

**Database Management:**
- Use Railway's MySQL client to manage database
- Or connect with TablePlus, DBeaver, etc.
- Backup database regularly

---

**Your site should now work perfectly on Railway!** 🚀

If you still see errors, share:
1. Your Railway domain URL
2. The exact error message
3. Output from `test_db_connection.php`
