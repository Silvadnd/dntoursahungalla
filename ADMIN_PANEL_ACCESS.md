# ✅ Admin Panel Access Guide

## 🔑 Login Credentials

The admin credentials are stored securely in the database with bcrypt encryption.

**To set credentials locally:**
1. Copy `.env.example` to `.env`
2. Update the values (do NOT commit `.env` to git)
3. Credentials are loaded from environment variables

⚠️ **IMPORTANT:** Never hardcode credentials in PHP files. Use environment variables instead.

---

## 🌐 How to Access Admin Panel

### Local Development (XAMPP)
```
URL: http://localhost/dntoursahungalla/php/admin_login.php
Username: admin
Password: (Set in your local .env file)
```

### Railway Production (After Deployment)
```
URL: https://yoursite.railway.app/php/admin_login.php
Username: admin
Password: (Set via Railway environment variables)
```

**Note:** Never hardcode passwords in PHP files. Always use environment variables.

---

## 📋 What You Can Do in Admin Panel

### 1. **Gallery Management** 
   - Upload multiple photos at once
   - Assign photos to categories
   - Delete unwanted photos
   - Organize tour photos

### 2. **Review Management**
   - View all customer reviews
   - See ratings and testimonials
   - Delete inappropriate reviews
   - Monitor customer feedback

### 3. **Contact Messages**
   - View messages from contact form
   - Read customer inquiries
   - Archive important messages

---

## 🔄 Login Process

1. **Open Admin Login Page**
   - Local: `http://localhost/dntoursahungalla/php/admin_login.php`
   - Railway: `https://yoursite.railway.app/php/admin_login.php`

2. **Enter Credentials**
   - Username: `admin`
   - Password: `admin123`

3. **Click "Login"**
   - Should see: Admin Panel Dashboard
   - Shows: Gallery Management tab (default)

4. **Dashboard Features**
   - **Gallery Management** - Upload and manage photos
   - **Review Management** - View and manage reviews
   - **Contact Messages** - View contact form submissions
   - **Logout** - Log out when done

---

## 📸 Gallery Management

### Upload Photos
1. Click "Gallery Management" tab
2. Click "Choose Files" (select multiple)
3. Enter category (optional)
4. Click "Upload Photos"
5. Photos appear in grid below

### Delete Photos
1. Hover over photo
2. Click "✕" button (top right)
3. Confirm deletion
4. Photo is removed

---

## ⭐ Review Management

### View Reviews
1. Click "Review Management" tab
2. See all customer reviews
3. Shows: Name, Rating, Date, Email
4. Full review text displayed

### Delete Reviews
1. Click "Review Management" tab
2. Find review to delete
3. Click "Delete" button
4. Confirm deletion

---

## 💬 Contact Messages

### View Messages
1. Click "Contact Messages" tab
2. See all contact form submissions
3. Shows: Name, Email, Message, Date
4. Can archive or delete

---

## 🚨 Troubleshooting

### "Invalid username or password"
**Solution:**
- ✓ Username is: `admin` (all lowercase)
- ✓ Password is: `admin123` (with "123")
- Check CAPS LOCK is off
- Make sure no extra spaces

### "Session not found" or "Not logged in"
**Solution:**
- Cookies must be enabled in browser
- Try private/incognito window
- Clear browser cache
- Try different browser

### "Page not found" (404 error)
**Solution:**
- Verify path: `/php/admin_login.php`
- Not: `/admin_login.php` (missing /php/)
- Make sure website is running
- Check Railway deployment status

### Can't access on Railway
**Solution:**
- Wait for deployment to complete (2-5 min)
- Check Railway logs for errors
- Verify MySQL connection working
- Try test page: `/test_railway_connection.php`

---

## 🔒 Security Tips

1. **Change Default Password** (Recommended)
   - Current: admin / admin123
   - Should update to strong password
   - Only you should know it

2. **Secure Access**
   - Always use HTTPS on production (Railway provides)
   - Don't share login credentials
   - Log out when done
   - Consider IP restrictions

3. **Backup Important Data**
   - Weekly database backups
   - Keep photos backed up
   - Archive old reviews

---

## 🔄 Password Management (If Needed)

### Change Your Admin Password

Use the secure password management endpoint:

1. **Create a simple PHP script** (not committed to git):
```php
<?php
// This script should only be run locally, not deployed
require_once 'php/config.php';
require_once 'php/auth.php';

$auth = new AdminAuth($pdo);

// Change to your new password
if ($auth->changePassword('admin', 'YourNewSecurePassword123')) {
    echo "✓ Password changed successfully";
} else {
    echo "✗ Failed to change password";
}
?>
```

2. **Or use the API endpoint:**
```php
POST /php/manage_password.php
Content-Type: application/json

{
    "action": "change_password",
    "username": "admin",
    "new_password": "YourNewSecurePassword123"
}
```

### Password Requirements
- Minimum 8 characters
- Stored with bcrypt encryption (cost=12)
- Never logged or displayed in plain text
- Always transmitted over HTTPS on production

---

## 📊 Admin Dashboard Layout

```
┌─ DN Tours Admin ──────────────────────────┐
│                                            │
│  Admin Panel                    [Logout]   │
│  ┌──────────────────────────────┐          │
│  │ Gallery │ Reviews │ Messages │          │
│  └──────────────────────────────┘          │
│                                            │
│  GALLERY MANAGEMENT (Default Tab)          │
│  ┌──────────────────────────────┐          │
│  │ Upload New Photos            │          │
│  │ [Choose Files] [Category]    │          │
│  │ [Upload Photos]              │          │
│  └──────────────────────────────┘          │
│                                            │
│  ┌─ Photo Grid ───────────────────┐        │
│  │ [Photo] [Photo] [Photo] [Photo]│        │
│  │ [Photo] [Photo] [Photo] [Photo]│        │
│  │ [Photo] [Photo] [Photo] [Photo]│        │
│  └────────────────────────────────┘        │
│                                            │
└────────────────────────────────────────────┘
```

---

## ✅ Admin Account Details

| Field | Value |
|-------|-------|
| Username | admin |
| Password | Securely hashed with bcrypt (cost=12) |
| User ID | 1 |
| Status | Active |
| Created | October 15, 2025 |
| Database | Railway MySQL |
| Authentication | php/auth.php (AdminAuth class) |

---

## 🎯 Quick Reference

| Action | URL | Method |
|--------|-----|--------|
| Login | `/php/admin_login.php` | POST |
| Dashboard | `/php/admin_panel.php` | GET |
| Logout | `/php/logout.php` | GET |
| Upload Photo | `/php/admin_panel.php` | POST |
| Delete Photo | `/php/admin_panel.php?delete_image=ID` | GET |
| Delete Review | `/php/admin_panel.php?delete_review=ID` | GET |

---

## 📱 Test Now

### Step 1: Start XAMPP
- Start Apache service
- Start MySQL service

### Step 2: Create .env File (Local Development Only)
- Copy `.env.example` to `.env`
- Update with your local/Railway credentials
- Keep `.env` in .gitignore (never commit)

### Step 3: Open Admin Login
- Go to: `http://localhost/dntoursahungalla/php/admin_login.php`

### Step 4: Login
- Username: `admin`
- Password: Check your `.env` file for ADMIN_PASSWORD
- Click: "Login"

### Step 5: Explore
- Try uploading a photo
- View existing reviews
- Check contact messages

---

## 🎉 You're All Set!

Your admin panel is ready to use! You can now:

✓ Upload and manage tour photos  
✓ View and manage customer reviews  
✓ Access contact form messages  
✓ Manage your tour website content  

**Security Features:**
- ✅ Passwords stored with bcrypt encryption (cost=12)
- ✅ Credentials in environment variables, not in code
- ✅ Session-based authentication with PHP
- ✅ HTTPS on production (Railway)
- ✅ Password verification through secure php/auth.php class

---

**Last Updated:** December 4, 2025  
**Status:** ✅ Secured with Encryption  
**Database:** Railway MySQL  
**Authentication:** php/auth.php (AdminAuth class) with bcrypt hashing
