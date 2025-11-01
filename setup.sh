#!/bin/bash
# Gatherly GEMS - Database Setup Script
# This script sets up the database and runs migrations

echo "=========================================="
echo "Gatherly GEMS - Database Setup"
echo "=========================================="
echo ""

# Configuration
DB_HOST="localhost"
DB_NAME="gatherly_db"
DB_USER="root"
DB_PASS=""

# Check if .env exists
if [ -f .env ]; then
    echo "✓ Found .env file"
    # Load credentials from .env
    DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
    DB_NAME=$(grep DB_NAME .env | cut -d '=' -f2)
    DB_USER=$(grep DB_USER .env | cut -d '=' -f2)
    DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
else
    echo "⚠ No .env file found. Using defaults..."
    echo "  Copying .env.example to .env"
    cp .env.example .env
    echo "  Please edit .env file with your database credentials"
    echo "  Then run this script again"
    exit 1
fi

echo ""
echo "Database Configuration:"
echo "  Host: $DB_HOST"
echo "  Database: $DB_NAME"
echo "  User: $DB_USER"
echo ""

# Test MySQL connection
echo "Testing MySQL connection..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS -e "SELECT 1" > /dev/null 2>&1

if [ $? -ne 0 ]; then
    echo "✗ Failed to connect to MySQL"
    echo "  Please check your database credentials in .env"
    exit 1
fi

echo "✓ MySQL connection successful"
echo ""

# Create database
echo "Creating database..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -eq 0 ]; then
    echo "✓ Database created or already exists"
else
    echo "✗ Failed to create database"
    exit 1
fi

echo ""

# Run migrations
echo "Running migrations..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < database/migrations/001_create_initial_schema.sql

if [ $? -eq 0 ]; then
    echo "✓ Migrations completed successfully"
else
    echo "✗ Failed to run migrations"
    exit 1
fi

echo ""

# Ask about seed data
read -p "Do you want to load seed data (test venues and users)? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Loading seed data..."
    mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < database/seeds/001_seed_data.sql
    
    if [ $? -eq 0 ]; then
        echo "✓ Seed data loaded successfully"
        echo ""
        echo "Test accounts created:"
        echo "  Admin: admin@gatherly.com / Admin@123"
        echo "  Manager: manager1@venues.com / Manager@123"
        echo "  Organizer: organizer1@events.com / Organizer@123"
    else
        echo "✗ Failed to load seed data"
        exit 1
    fi
fi

echo ""
echo "=========================================="
echo "✓ Database setup completed successfully!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "  1. Start your Apache and MySQL servers"
echo "  2. Visit: http://localhost/Gatherly-2025/frontend"
echo "  3. Test API: http://localhost/Gatherly-2025/backend/api/health"
echo ""
echo "Documentation:"
echo "  - API Documentation: docs/API.md"
echo "  - Deployment Guide: docs/DEPLOYMENT.md"
echo "  - Architecture: docs/ARCHITECTURE.md"
echo ""
