#!/bin/bash

# Migration script to update room field from int to varchar
# This script connects to the MySQL container and runs the migration

echo "🔄 Starting room field migration from int to varchar..."

# Run the migration SQL script
docker compose exec db mysql -u root -prootpassword work_orders < database/migrate_room_to_varchar.sql

if [ $? -eq 0 ]; then
    echo "✅ Migration completed successfully!"
    echo "   Room field is now varchar(100) and can accept text values like 'Room 101', 'Building A', etc."
else
    echo "❌ Migration failed. Please check the error messages above."
    exit 1
fi

echo ""
echo "🔍 Verifying the migration..."

# Verify the schema change
docker compose exec db mysql -u root -prootpassword work_orders -e "DESCRIBE orders;"

echo ""
echo "📋 Current room data after migration:"
docker compose exec db mysql -u root -prootpassword work_orders -e "SELECT id, room, work_to_be_done FROM orders LIMIT 5;"