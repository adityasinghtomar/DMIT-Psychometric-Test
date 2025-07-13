# DMIT Psychometric Test System

A comprehensive, secure PHP-based psychometric assessment platform implementing DMIT (Dermatoglyphics Multiple Intelligence Test) methodology for career guidance and personality analysis.

## 🚀 Features

### Core Functionality
- **Biometric Data Collection**: Fingerprint pattern analysis and ridge counting
- **Multiple Intelligence Assessment**: Based on Howard Gardner's theory
- **Personality Profiling**: DISC and animal-based personality types
- **Brain Dominance Analysis**: Left/right brain preference assessment
- **Learning Style Analysis**: VAK (Visual, Auditory, Kinesthetic) preferences
- **Career Recommendations**: RIASEC-based career mapping
- **Professional PDF Reports**: Dynamic, chart-rich assessment reports

### Security Features
- **Multi-layer Security**: SQL injection prevention, XSS protection, CSRF tokens
- **Secure Authentication**: Password hashing, session management, rate limiting
- **Data Encryption**: Sensitive data encryption at rest
- **Audit Logging**: Comprehensive security event tracking
- **Role-based Access Control**: Admin, Counselor, and User roles
- **Session Security**: Automatic timeout, session regeneration

### Technical Features
- **Responsive Design**: Bootstrap 5-based modern UI
- **Database Security**: Prepared statements, input validation
- **File Upload Security**: Type validation, secure storage
- **Error Handling**: Comprehensive error logging and user feedback
- **Performance Optimized**: Efficient database queries and caching

## 📋 Requirements

### Server Requirements
- **PHP**: 7.4 or higher (8.0+ recommended)
- **MySQL**: 5.7 or higher (8.0+ recommended)
- **Web Server**: Apache or Nginx
- **Extensions**: PDO, OpenSSL, GD, mbstring, fileinfo

### Development Environment
- **XAMPP**: Recommended for local development
- **Composer**: For dependency management (optional)
- **Git**: For version control

## 🛠️ Installation

### 1. Download and Setup
```bash
# Clone or download the project
git clone <repository-url>
cd DMIT-Psychometric-Test

# Or download and extract to your web server directory
# For XAMPP: C:\xampp\htdocs\DMIT-Psychometric-Test
```

### 2. Database Configuration
1. Start your MySQL server (XAMPP Control Panel)
2. Open your web browser and navigate to:
   ```
   http://localhost/DMIT-Psychometric-Test/install/install.php
   ```

### 3. Installation Wizard
Fill in the installation form with:

**Database Configuration:**
- Database Host: `localhost`
- Database Name: `dmit_psychometric` (or your preferred name)
- Database Username: `root`
- Database Password: (leave blank for XAMPP default)

**Admin Account:**
- Username: Your preferred admin username
- Email: Your email address
- Password: Strong password (min 8 chars with uppercase, lowercase, number, special char)
- First Name: Your first name
- Last Name: Your last name

### 4. Post-Installation
After successful installation:
1. Delete the `install` directory for security
2. Set proper file permissions (755 for directories, 644 for files)
3. Configure email settings in `config/config.php` if needed

## 🔐 Security Configuration

### Essential Security Steps

1. **Change Default Encryption Key**
   ```php
   // In config/config.php
   define('ENCRYPTION_KEY', 'your-unique-32-character-key');
   ```

2. **Configure HTTPS** (Production)
   ```php
   // In config/config.php
   ini_set('session.cookie_secure', 1); // Enable for HTTPS
   ```

3. **Set Strong Passwords**
   - Change default admin password immediately
   - Enforce strong password policies

4. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 reports/
   chmod 644 config/*.php
   ```

5. **Database Security**
   - Create dedicated database user with limited privileges
   - Use strong database passwords
   - Enable MySQL SSL if available

## 📖 Usage Guide

### For Administrators
1. **User Management**: Create and manage user accounts
2. **System Settings**: Configure application settings
3. **Security Monitoring**: Review audit logs and security events
4. **Report Management**: Oversee all assessments and reports

### For Counselors
1. **Assessment Creation**: Create assessments for multiple subjects
2. **Report Generation**: Generate and customize reports
3. **Client Management**: Manage assessment subjects and their data

### For Users
1. **Self Assessment**: Create personal assessments
2. **Report Access**: View and download personal reports
3. **Profile Management**: Update personal information

### Assessment Process
1. **Subject Information**: Enter basic demographic data
2. **Fingerprint Collection**: Upload or scan fingerprint images
3. **Data Analysis**: System processes biometric data
4. **Report Generation**: Comprehensive PDF report creation

## 🔧 Configuration

### Email Settings
Configure SMTP settings in `config/config.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

### File Upload Settings
Adjust upload limits in `config/config.php`:
```php
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png']);
```

### Session Configuration
Modify session settings:
```php
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes
```

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check MySQL service is running
   - Verify database credentials
   - Ensure database exists

2. **File Upload Errors**
   - Check PHP upload_max_filesize setting
   - Verify directory permissions
   - Ensure uploads directory exists

3. **Session Issues**
   - Check PHP session configuration
   - Verify session directory permissions
   - Clear browser cookies

4. **Permission Denied**
   - Set correct file permissions
   - Check web server user ownership
   - Verify .htaccess files

### Error Logs
Check error logs in:
- PHP error log
- Web server error log
- Application logs in `logs/` directory

## 🔒 Security Best Practices

1. **Regular Updates**: Keep PHP and MySQL updated
2. **Backup Strategy**: Regular database and file backups
3. **Monitor Logs**: Review security events regularly
4. **Access Control**: Limit admin access to trusted users
5. **SSL Certificate**: Use HTTPS in production
6. **Firewall**: Configure server firewall rules
7. **Database Security**: Regular security audits

## 📊 System Architecture

### Database Schema
- **Users**: Authentication and user management
- **Assessment Subjects**: Subject information and demographics
- **Fingerprint Data**: Biometric data storage (encrypted)
- **Intelligence Scores**: Multiple intelligence analysis results
- **Reports**: Generated assessment reports
- **Audit Logs**: Security and activity tracking

### File Structure
```
DMIT-Psychometric-Test/
├── config/          # Configuration files
├── includes/        # Common PHP includes
├── auth/           # Authentication system
├── assessments/    # Assessment management
├── reports/        # Report generation
├── admin/          # Admin panel
├── uploads/        # File uploads (secured)
├── database/       # Database schema
└── install/        # Installation wizard
```

## 🤝 Support

For technical support or questions:
1. Check the troubleshooting section
2. Review error logs
3. Contact system administrator
4. Refer to documentation

## 📄 License

This project is proprietary software. All rights reserved.

## 🔄 Version History

- **v1.0.0**: Initial release with core DMIT functionality
- Security-first architecture
- Comprehensive assessment system
- Professional report generation

---

**Note**: This system handles sensitive biometric and personal data. Ensure compliance with local privacy regulations and implement appropriate security measures for production use.
