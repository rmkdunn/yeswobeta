# Property Management System - Docker Setup

This guide explains how to run the Property Management System using Docker containers.

## 🐳 Prerequisites

- [Docker](https://www.docker.com/get-started) installed on your system
- [Docker Compose](https://docs.docker.com/compose/install/) (usually included with Docker Desktop)

## 🚀 Quick Start

### 1. Clone and Navigate to the Project
```bash
git clone <repository-url>
cd yeswobeta
```

### 2. Start the Application
```bash
docker-compose up -d
```

This command will:
- Build the PHP application container
- Start MySQL database
- Initialize the database with the schema
- Start phpMyAdmin for database management

### 3. Access the Application

- **Main Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081 (optional database management)
  - Username: `root`
  - Password: `rootpassword`

## 📋 Container Services

### Web Server (PHP + Apache)
- **Port**: 8080
- **Technology**: PHP 8.2 with Apache
- **Features**: 
  - PDO MySQL support
  - File upload capabilities
  - Session management

### Database (MySQL 8.0)
- **Port**: 3306
- **Database**: `work_orders`
- **Root Password**: `rootpassword`
- **Auto-initialization**: Loads schema from `database/work_orders.sql`

### phpMyAdmin
- **Port**: 8081
- **Purpose**: Database administration interface

## 🛠️ Development Commands

### View Running Containers
```bash
docker-compose ps
```

### View Logs
```bash
# All services
docker-compose logs

# Specific service
docker-compose logs web
docker-compose logs db
```

### Stop the Application
```bash
docker-compose down
```

### Rebuild and Restart
```bash
docker-compose down
docker-compose up -d --build
```

### Access Container Shell
```bash
# Web server container
docker-compose exec web bash

# Database container
docker-compose exec db mysql -u root -p
```

## 📁 Volume Mounts

- `./uploads:/var/www/html/uploads` - File uploads persist on host
- `./logs:/var/log/apache2` - Apache logs accessible on host
- `db_data` - MySQL data persists across container restarts

## ⚙️ Environment Configuration

The application uses environment variables defined in `.env`:

```env
# Database Configuration
DB_HOST=db
DB_NAME=work_orders
DB_USER=root
DB_PASSWORD=rootpassword
```

### Custom Configuration
1. Copy `.env` to `.env.local`
2. Modify values in `.env.local`
3. Update `docker-compose.yml` to use your custom file

## 🔧 Troubleshooting

### Database Connection Issues
1. Ensure containers are running: `docker-compose ps`
2. Check database logs: `docker-compose logs db`
3. Verify environment variables in `.env`

### File Upload Problems
1. Check uploads directory permissions:
   ```bash
   docker-compose exec web ls -la uploads/
   ```
2. Verify volume mount is working:
   ```bash
   ls -la uploads/
   ```

### Application Errors
1. Check PHP/Apache logs:
   ```bash
   docker-compose logs web
   ```
2. Access container to debug:
   ```bash
   docker-compose exec web bash
   ```

## 🔐 Security Notes

### Production Deployment
- Change default passwords in `.env`
- Remove phpMyAdmin service for production
- Use SSL/HTTPS with proper certificates
- Restrict database access

### Default Credentials
⚠️ **Change these for production use:**
- MySQL root password: `rootpassword`
- phpMyAdmin access: `root` / `rootpassword`

## 📦 File Structure
```
yeswobeta/
├── docker-compose.yml      # Container orchestration
├── Dockerfile             # Web server image definition
├── .env                   # Environment variables
├── .dockerignore         # Docker build exclusions
├── docker/
│   └── apache-config.conf # Apache virtual host config
├── config/               # Application configuration
├── includes/            # PHP includes (header/footer)
├── auth/               # Authentication pages
├── pages/              # Main application pages
├── modules/            # Feature modules
├── uploads/            # File uploads (volume mounted)
├── assets/             # Static assets
├── database/           # SQL schema files
└── docs/              # Documentation
```

## 🔄 Database Management

### Import Additional Data
```bash
# Copy SQL file to container
docker cp your_data.sql $(docker-compose ps -q db):/tmp/

# Execute SQL
docker-compose exec db mysql -u root -prootpassword work_orders < /tmp/your_data.sql
```

### Backup Database
```bash
docker-compose exec db mysqldump -u root -prootpassword work_orders > backup.sql
```

### Reset Database
```bash
docker-compose down
docker volume rm yeswobeta_db_data
docker-compose up -d
```

## 🎯 Next Steps

1. **Setup First User**: Navigate to http://localhost:8080 and register a user account
2. **Configure Application**: Update any application-specific settings
3. **Upload Data**: Import any existing work orders or data
4. **Customize**: Modify the application as needed for your requirements

## 📞 Support

For issues or questions:
1. Check the application logs: `docker-compose logs`
2. Verify all containers are running: `docker-compose ps`
3. Review this documentation
4. Check Docker and Docker Compose installation