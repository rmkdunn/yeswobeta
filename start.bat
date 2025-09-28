@echo off
title Property Management System - Docker Setup

echo 🏗️  Property Management System - Docker Setup
echo ==============================================

REM Check if Docker is installed
docker --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker is not installed. Please install Docker Desktop first.
    echo    Visit: https://www.docker.com/get-started
    pause
    exit /b 1
)

REM Check if Docker Compose is installed
docker-compose --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker Compose is not installed. Please install Docker Desktop with Compose.
    echo    Visit: https://www.docker.com/get-started
    pause
    exit /b 1
)

echo ✅ Docker and Docker Compose are installed

REM Check if .env file exists
if not exist .env (
    echo ❌ .env file not found. Using default configuration...
    echo ℹ️  For custom settings, create a .env file
)

echo ✅ Environment configuration ready

echo.
echo 🚀 Starting Property Management System...
echo    This may take a few minutes on first run...
echo.

docker-compose up -d

if %errorlevel% equ 0 (
    echo.
    echo 🎉 Application started successfully!
    echo.
    echo 📱 Access your application:
    echo    Main App:    http://localhost:8080
    echo    phpMyAdmin:  http://localhost:8081
    echo.
    echo 🔍 Useful commands:
    echo    View logs:     docker-compose logs -f
    echo    Stop app:      docker-compose down
    echo    Restart:       docker-compose restart
    echo.
    echo 📖 For more information, see DOCKER.md
    echo.
    echo Press any key to open the application in your browser...
    pause >nul
    start http://localhost:8080
) else (
    echo ❌ Failed to start application. Check the logs with:
    echo    docker-compose logs
    pause
)