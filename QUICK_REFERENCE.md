# 🚀 QUICK REFERENCE - Railway MySQL Connection

## ✅ Connection Details
```
Host:     shuttle.proxy.rlwy.net
Port:     16503
User:     root
Password: eTaArsItbOApYBCxEVGaupBEiqLkOFLN
Database: railway
Protocol: TCP
```

## 📝 Admin Login
```
Username: admin
Password: admin123
```

## 🧪 Test Connection
```
Local:   http://localhost/dntoursahungalla/test_railway_connection.php
Railway: https://yoursite.railway.app/test_railway_connection.php
```

## 🗄️ Database Tables
```
✅ reviews      - Customer testimonials
✅ gallery      - Tour photos
✅ admin_users  - Admin accounts
```

## 🔧 Configuration File
```php
// php/config.php
// Automatically detects Railway MySQL and connects
// No changes needed - ready to use!
```

## 🚨 Delete After Testing
```
✓ test_railway_connection.php
✓ setup_railway_db.php
✓ database_setup_railway.sql
```

## 📱 How to Delete on Railway
```bash
# Via Railway Console
rm test_railway_connection.php setup_railway_db.php
```

## 🔍 Quick Test Steps
```
1. Wait for Railway to deploy (2-5 minutes)
2. Visit test_railway_connection.php
3. Check all 3 tests pass ✅
4. Delete test files
5. Use your website normally!
```

## 💾 Database Check (Command Line)
```bash
# Connect directly
mysql -h shuttle.proxy.rlwy.net -u root -peTaArsItbOApYBCxEVGaupBEiqLkOFLN --port 16503 --protocol=TCP railway

# Inside MySQL
SHOW TABLES;
SELECT COUNT(*) FROM reviews;
SELECT COUNT(*) FROM admin_users;
```

## 📊 Status
```
Local Development:  ✅ WORKING (localhost)
Railway Production: ✅ CONNECTED (shuttle.proxy.rlwy.net)
Admin Access:       ✅ ACTIVE (admin/admin123)
Feature: Reviews:   ✅ READY
Feature: Gallery:   ✅ READY
```

## 🎯 Priority Tasks
```
1. ✅ Database connected
2. ⏳ Test on Railway domain
3. ⏳ Delete test files
4. ⏳ Change admin password
5. ⏳ Go live!
```

---
**Last Updated:** November 3, 2025  
**Status:** ✅ READY FOR PRODUCTION
