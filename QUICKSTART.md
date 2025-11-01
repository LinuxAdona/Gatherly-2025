# Gatherly GEMS - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- Web browser
- Text editor

---

## Step 1: Database Setup

### Windows Users
1. Open Command Prompt in project directory
2. Run the setup script:
   ```cmd
   setup.bat
   ```
3. Follow the prompts to create database and load seed data

### Linux/Mac Users
1. Open Terminal in project directory
2. Make script executable:
   ```bash
   chmod +x setup.sh
   ```
3. Run the setup script:
   ```bash
   ./setup.sh
   ```
4. Follow the prompts to create database and load seed data

### Manual Setup (Alternative)
1. Start XAMPP and open phpMyAdmin (http://localhost/phpmyadmin)
2. Create database: `gatherly_db`
3. Import SQL files in order:
   - `database/migrations/001_create_initial_schema.sql`
   - `database/seeds/001_seed_data.sql` (optional, for test data)

---

## Step 2: Configure Environment

1. Copy `.env.example` to `.env`:
   ```bash
   # Windows
   copy .env.example .env
   
   # Linux/Mac
   cp .env.example .env
   ```

2. Edit `.env` file with your settings:
   ```env
   DB_HOST=localhost
   DB_NAME=gatherly_db
   DB_USER=root
   DB_PASSWORD=
   
   JWT_SECRET=your_secret_key_here
   ```

---

## Step 3: Start Services

1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service

---

## Step 4: Test Installation

### 1. Test API Health
Open browser and visit:
```
http://localhost/Gatherly-2025/backend/api/health
```

Expected response:
```json
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

### 2. View Landing Page
Open browser and visit:
```
http://localhost/Gatherly-2025/frontend
```

---

## Step 5: Test User Accounts

### Test Login Credentials
After loading seed data, you can login with:

**Admin Account:**
- Email: `admin@gatherly.com`
- Password: `Admin@123`

**Venue Manager:**
- Email: `manager1@venues.com`
- Password: `Manager@123`

**Event Organizer:**
- Email: `organizer1@events.com`
- Password: `Organizer@123`

---

## 📝 Testing the API

### Using cURL

#### Register New User
```bash
curl -X POST http://localhost/Gatherly-2025/backend/api/auth/register \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"test@example.com\",
    \"password\": \"Test@123456\",
    \"full_name\": \"Test User\",
    \"phone\": \"+639171234567\",
    \"role\": \"organizer\"
  }"
```

#### Login
```bash
curl -X POST http://localhost/Gatherly-2025/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"admin@gatherly.com\",
    \"password\": \"Admin@123\"
  }"
```

#### Get Profile (requires token)
```bash
curl -X GET http://localhost/Gatherly-2025/backend/api/auth/me \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

### Using Postman
1. Import the API collection (coming soon)
2. Set base URL: `http://localhost/Gatherly-2025/backend/api`
3. Test endpoints from the collection

---

## 🗂️ Project Structure

```
Gatherly-2025/
├── backend/              # PHP backend
│   ├── config/          # Configuration files
│   ├── controllers/     # Request handlers
│   ├── models/          # Data models
│   ├── middleware/      # Auth & validation
│   ├── routes/          # API routes
│   ├── utils/           # Helper utilities
│   └── index.php        # Entry point
├── frontend/            # HTML/CSS/JS frontend
│   ├── assets/          # Static assets
│   ├── pages/           # HTML pages
│   └── index.html       # Landing page
├── database/            # SQL files
│   ├── migrations/      # Schema files
│   └── seeds/           # Test data
├── docs/                # Documentation
│   ├── API.md
│   ├── DEPLOYMENT.md
│   ├── ARCHITECTURE.md
│   └── CYCLE_1_RELEASE.md
├── ml/                  # Python ML services
├── tests/               # Test files
├── .env.example         # Environment template
├── setup.bat            # Windows setup
└── setup.sh             # Linux/Mac setup
```

---

## 📚 Next Steps

1. **Read Documentation**
   - [API Documentation](docs/API.md)
   - [System Architecture](docs/ARCHITECTURE.md)
   - [Deployment Guide](docs/DEPLOYMENT.md)

2. **Explore Sample Data**
   - 6 sample venues with different types
   - 15 amenities
   - 90 days of availability
   - Dynamic pricing rules

3. **Start Development**
   - Check roadmap in README.md
   - See open issues for tasks
   - Review Cycle 1 release notes

---

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP
- Check credentials in `.env` file
- Ensure database `gatherly_db` exists

### 404 Errors on API
- Check Apache is running
- Verify mod_rewrite is enabled
- Check `.htaccess` file exists in backend/

### JWT Token Errors
- Set `JWT_SECRET` in `.env` file
- Ensure token format: `Bearer YOUR_TOKEN`
- Check token hasn't expired

### Permission Denied (Linux/Mac)
```bash
chmod -R 755 backend/
chmod -R 777 logs/
chmod -R 777 contracts/
```

---

## 📧 Support

- **Documentation**: Check `docs/` folder
- **Issues**: Create GitHub issue
- **Email**: support@gatherly.com

---

## 🎉 Success!

If you've completed all steps successfully:
- ✅ Database is set up
- ✅ API is running
- ✅ Frontend is accessible
- ✅ Test accounts are working

You're ready to start using Gatherly GEMS!

Happy coding! 🚀
