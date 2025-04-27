# MCQ Exam System

A PHP-based Multiple Choice Question (MCQ) examination system.

## Deployment Instructions for Infinity

1. **Database Setup**
   - Create a new MySQL database on Infinity
   - Import the database schema using `database.sql` and `database_updates.sql`
   - Update `config/database.prod.php` with your Infinity database credentials

2. **File Upload**
   - Upload all project files to your Infinity hosting directory
   - Ensure proper file permissions (typically 644 for files, 755 for directories)
   - Make sure the `config` directory is not publicly accessible

3. **Configuration**
   - Rename `config/database.prod.php` to `config/database.php`
   - Update database credentials in the configuration file
   - Ensure PHP version is 7.4 or higher

4. **Security**
   - The `.htaccess` file is configured for basic security
   - All sensitive files are protected from direct access
   - HTTPS is enforced

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- mod_rewrite enabled
- PDO PHP Extension
- mbstring PHP Extension

## Features

- User registration and authentication
- Admin dashboard for exam management
- Multiple choice question creation
- Exam taking interface
- Result tracking and analysis
- Category-based exam organization
- Password reset functionality

## Support

For any issues or questions, please contact the system administrator. 