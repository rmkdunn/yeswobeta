#!/bin/bash

# Property Management System - Docker Startup Script

echo "🏗️  Property Management System - Docker Setup"
echo "=============================================="

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    echo "   Visit: https://www.docker.com/get-started"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    echo "   Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

echo "✅ Docker and Docker Compose are installed"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ .env file not found. Please create one based on .env.example"
    exit 1
fi

echo "✅ Environment file found"

# Start the application
echo ""
echo "🚀 Starting Property Management System..."
echo "   This may take a few minutes on first run..."

docker-compose up -d

if [ $? -eq 0 ]; then
    echo ""
    echo "🎉 Application started successfully!"
    echo ""
    echo "📱 Access your application:"
    echo "   Main App:    http://localhost:8080"
    echo "   phpMyAdmin:  http://localhost:8081"
    echo ""
    echo "🔍 Useful commands:"
    echo "   View logs:     docker-compose logs -f"
    echo "   Stop app:      docker-compose down"
    echo "   Restart:       docker-compose restart"
    echo ""
    echo "📖 For more information, see DOCKER.md"
else
    echo "❌ Failed to start application. Check the logs with:"
    echo "   docker-compose logs"
fi