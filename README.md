# DN Tours Ahungalla

A professional tour guide website for DN Tours in Ahungalla, Sri Lanka.

## Features

- Responsive website design
- Gallery with image uploads
- Customer reviews system
- Admin panel for content management
- Contact form with email notifications
- WhatsApp integration

## Local Development

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer

### Installation

1. Clone the repository:
```bash
git clone https://github.com/Silvadnd/dntoursahungalla.git
cd dntoursahungalla
```

2. Install dependencies:
```bash
composer install
```

3. Create the database:
```bash
mysql -u root -p < database_setup.sql
```

4. Configure your database in `php/config.php` or set environment variables:
- `DB_HOST` (default: localhost)
- `DB_NAME` (default: dn_tours)
- `DB_USER` (default: root)
- `DB_PASSWORD` (default: empty)
- `DB_PORT` (default: 3306)

5. Start your local server:
```bash
php -S localhost:8000
```

## Railway Deployment

### Prerequisites
1. Create a Railway account at https://railway.app
2. Install Railway CLI (optional): `npm i -g @railway/cli`

### Database Setup on Railway

1. **Add MySQL Database:**
   - Go to your Railway project
   - Click "New" → "Database" → "Add MySQL"
   - Railway will automatically create the database and provide connection details

2. **Import Database Schema:**
   - Connect to your Railway MySQL database
   - Run the SQL commands from `database_setup.sql`
   - Or use Railway's MySQL client to import the file

### Application Deployment

1. **Connect GitHub Repository:**
   - In Railway, click "New" → "GitHub Repo"
   - Select `Silvadnd/dntoursahungalla`
   - Railway will automatically detect PHP and deploy

2. **Set Environment Variables:**
   In your Railway project settings, add these variables:
   ```
   DB_HOST=<your-railway-mysql-host>
   DB_NAME=<your-railway-mysql-database>
   DB_USER=<your-railway-mysql-user>
   DB_PASSWORD=<your-railway-mysql-password>
   DB_PORT=3306
   ```
   
   These values are automatically available when you add a MySQL database to your Railway project.

3. **Deploy:**
   - Railway will automatically deploy your application
   - Your site will be available at: `https://<your-app>.railway.app`

### Troubleshooting Railway Deployment

**Issue: "Could not find driver" error**
- Solution: The `nixpacks.toml` file ensures PDO MySQL extensions are installed

**Issue: 403 Forbidden error**
- Solution: The updated `.htaccess` file fixes permission issues
- Make sure `index.php` is accessible at root

**Issue: Database connection failed**
- Check that environment variables are set correctly
- Verify MySQL database is running in Railway
- Check that database schema has been imported

### Environment Variables Reference

| Variable | Description | Default |
|----------|-------------|---------|
| DB_HOST | MySQL host | localhost |
| DB_NAME | Database name | dn_tours |
| DB_USER | Database user | root |
| DB_PASSWORD | Database password | (empty) |
| DB_PORT | Database port | 3306 |

## Default Admin Credentials

- Username: `admin`
- Password: `admin123`

**Important:** Change these credentials after first login!

## Project Structure

```
dntoursahungalla/
├── assets/          # Images and media files
├── css/             # Stylesheets
├── js/              # JavaScript files
├── php/             # PHP backend files
│   ├── config.php   # Database configuration
│   ├── admin_panel.php
│   ├── gallery.php
│   └── ...
├── vendor/          # Composer dependencies
├── index.php        # Homepage
├── database_setup.sql
├── .htaccess        # Apache configuration
├── nixpacks.toml    # Railway build configuration
└── README.md
```

## Support

For issues or questions, contact:
- Email: dinethrap2002@gmail.com
- WhatsApp: +94779452473

## License

© 2025 DN Tours Ahungalla. All rights reserved.
