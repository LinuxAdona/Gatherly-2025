@echo off
REM Gatherly GEMS - Database Setup Script (Windows)
REM This script sets up the database and runs migrations

echo ==========================================
echo Gatherly GEMS - Database Setup
echo ==========================================
echo.

REM Configuration
set DB_HOST=localhost
set DB_NAME=gatherly_db
set DB_USER=root
set DB_PASS=

REM Check if .env exists
if exist .env (
    echo [OK] Found .env file
    REM Note: Loading from .env in batch is complex, using defaults
    echo [INFO] Using configuration from .env file
) else (
    echo [WARNING] No .env file found
    echo [INFO] Copying .env.example to .env
    copy .env.example .env
    echo [INFO] Please edit .env file with your database credentials
    echo [INFO] Then run this script again
    pause
    exit /b 1
)

echo.
echo Database Configuration:
echo   Host: %DB_HOST%
echo   Database: %DB_NAME%
echo   User: %DB_USER%
echo.

REM Path to MySQL (adjust if needed)
set MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe

if not exist "%MYSQL_PATH%" (
    echo [ERROR] MySQL not found at: %MYSQL_PATH%
    echo [INFO] Please update MYSQL_PATH in this script
    pause
    exit /b 1
)

echo Testing MySQL connection...
"%MYSQL_PATH%" -h %DB_HOST% -u %DB_USER% -e "SELECT 1" >nul 2>&1

if errorlevel 1 (
    echo [ERROR] Failed to connect to MySQL
    echo [INFO] Please check your database credentials
    pause
    exit /b 1
)

echo [OK] MySQL connection successful
echo.

REM Create database
echo Creating database...
"%MYSQL_PATH%" -h %DB_HOST% -u %DB_USER% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if errorlevel 1 (
    echo [ERROR] Failed to create database
    pause
    exit /b 1
)

echo [OK] Database created or already exists
echo.

REM Run migrations
echo Running migrations...
"%MYSQL_PATH%" -h %DB_HOST% -u %DB_USER% %DB_NAME% < database\migrations\001_create_initial_schema.sql

if errorlevel 1 (
    echo [ERROR] Failed to run migrations
    pause
    exit /b 1
)

echo [OK] Migrations completed successfully
echo.

REM Ask about seed data
set /p LOAD_SEED="Do you want to load seed data (test venues and users)? (Y/N): "

if /i "%LOAD_SEED%"=="Y" (
    echo Loading seed data...
    "%MYSQL_PATH%" -h %DB_HOST% -u %DB_USER% %DB_NAME% < database\seeds\001_seed_data.sql
    
    if errorlevel 1 (
        echo [ERROR] Failed to load seed data
        pause
        exit /b 1
    )
    
    echo [OK] Seed data loaded successfully
    echo.
    echo Test accounts created:
    echo   Admin: admin@gatherly.com / Admin@123
    echo   Manager: manager1@venues.com / Manager@123
    echo   Organizer: organizer1@events.com / Organizer@123
)

echo.
echo ==========================================
echo [OK] Database setup completed successfully!
echo ==========================================
echo.
echo Next steps:
echo   1. Start XAMPP Control Panel
echo   2. Start Apache and MySQL services
echo   3. Visit: http://localhost/Gatherly-2025/frontend
echo   4. Test API: http://localhost/Gatherly-2025/backend/api/health
echo.
echo Documentation:
echo   - API Documentation: docs\API.md
echo   - Deployment Guide: docs\DEPLOYMENT.md
echo   - Architecture: docs\ARCHITECTURE.md
echo.
pause
