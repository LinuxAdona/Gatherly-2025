# Gatherly GEMS - Deployment Guide

## Prerequisites

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Python 3.8+ (for ML services)
- Node.js 14+ (for Socket.IO chat)

### XAMPP Setup (Development)
1. Install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services
3. Access phpMyAdmin at http://localhost/phpmyadmin

---

## Installation Steps

### 1. Clone Repository
```bash
git clone https://github.com/LinuxAdona/Gatherly-2025.git
cd Gatherly-2025
```

### 2. Configure Environment
```bash
# Copy example environment file
cp .env.example .env

# Edit .env file with your configuration
notepad .env  # Windows
nano .env     # Linux/Mac
```

**Important Variables:**
```env
DB_HOST=localhost
DB_NAME=gatherly_db
DB_USER=root
DB_PASSWORD=your_password

JWT_SECRET=generate_strong_random_secret_key
GOOGLE_MAPS_API_KEY=your_google_maps_key
```

### 3. Database Setup

#### Option A: Using phpMyAdmin
1. Open http://localhost/phpmyadmin
2. Create new database: `gatherly_db`
3. Import SQL files:
   - database/migrations/001_create_initial_schema.sql
   - database/seeds/001_seed_data.sql

#### Option B: Using Command Line
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE gatherly_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
mysql -u root -p gatherly_db < database/migrations/001_create_initial_schema.sql

# Run seeds (optional - for test data)
mysql -u root -p gatherly_db < database/seeds/001_seed_data.sql
```

### 4. Set Permissions (Linux/Mac)
```bash
chmod -R 755 backend/
chmod -R 777 logs/
chmod -R 777 contracts/
chmod -R 777 uploads/
```

### 5. Test API
```bash
# Test health endpoint
curl http://localhost/Gatherly-2025/backend/api/health

# Expected response:
{
  "success": true,
  "message": "API is running",
  "data": {
    "status": "healthy",
    "timestamp": "2025-11-01T10:00:00+08:00",
    "version": "1.0.0"
  }
}
```

---

## Python ML Services Setup

### 1. Create Virtual Environment
```bash
cd ml
python -m venv venv

# Activate virtual environment
# Windows:
venv\Scripts\activate

# Linux/Mac:
source venv/bin/activate
```

### 2. Install Dependencies
```bash
pip install scikit-learn pandas numpy flask prophet requests mysql-connector-python
```

### 3. Start ML Service
```bash
python ml_service.py
# Service runs on http://localhost:5000
```

---

## Socket.IO Chat Server Setup

### 1. Install Node.js Dependencies
```bash
cd chat-server
npm init -y
npm install socket.io express cors
```

### 2. Start Chat Server
```bash
node server.js
# Server runs on http://localhost:3000
```

---

## Apache Configuration

### Enable mod_rewrite
```apache
# In httpd.conf, uncomment:
LoadModule rewrite_module modules/mod_rewrite.so

# Change AllowOverride to:
<Directory "C:/xampp/htdocs">
    AllowOverride All
</Directory>
```

### Virtual Host (Optional)
```apache
<VirtualHost *:80>
    ServerName gatherly.local
    DocumentRoot "C:/xampp/htdocs/Gatherly-2025/frontend"
    
    <Directory "C:/xampp/htdocs/Gatherly-2025">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    Alias /api "C:/xampp/htdocs/Gatherly-2025/backend"
</VirtualHost>
```

---

## Testing

### Test User Accounts
After running seed data, you can login with:

**Admin:**
- Email: admin@gatherly.com
- Password: Admin@123

**Venue Manager:**
- Email: manager1@venues.com
- Password: Manager@123

**Event Organizer:**
- Email: organizer1@events.com
- Password: Organizer@123

### API Testing Tools
- Postman: Import collection from `docs/postman_collection.json`
- cURL: See examples in `docs/API.md`
- Browser: http://localhost/Gatherly-2025/frontend

---

## Production Deployment

### Security Checklist
- [ ] Change all default passwords
- [ ] Generate strong JWT_SECRET
- [ ] Set APP_DEBUG=false in .env
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall rules
- [ ] Set up regular database backups
- [ ] Implement rate limiting
- [ ] Configure CORS properly
- [ ] Remove test payment credentials
- [ ] Set secure session cookies

### Performance Optimization
- Enable PHP OpCache
- Configure MySQL query caching
- Use CDN for static assets
- Enable Gzip compression
- Implement Redis/Memcached caching

### Monitoring
- Set up error logging
- Configure uptime monitoring
- Implement performance tracking
- Set up database backup automation

---

## Troubleshooting

### Common Issues

**1. Database Connection Failed**
- Check MySQL is running
- Verify credentials in .env
- Ensure database exists

**2. 404 Errors on API**
- Check Apache mod_rewrite is enabled
- Verify .htaccess file exists
- Check file permissions

**3. JWT Token Issues**
- Verify JWT_SECRET is set
- Check token expiration time
- Ensure Authorization header format: "Bearer TOKEN"

**4. CORS Errors**
- Check CORS headers in config.php
- Verify frontend domain is allowed
- Check preflight OPTIONS requests

---

## Support

For deployment assistance:
- Email: support@gatherly.com
- Documentation: https://docs.gatherly.com
- GitHub Issues: https://github.com/LinuxAdona/Gatherly-2025/issues
