@echo off
title Room Field Migration - Int to Varchar

echo 🔄 Starting room field migration from int to varchar...
echo.

REM Run the migration SQL script
docker compose exec db mysql -u root -prootpassword work_orders < database/migrate_room_to_varchar.sql

if %errorlevel% equ 0 (
    echo ✅ Migration completed successfully!
    echo    Room field is now varchar^(100^) and can accept text values like 'Room 101', 'Building A', etc.
) else (
    echo ❌ Migration failed. Please check the error messages above.
    pause
    exit /b 1
)

echo.
echo 🔍 Verifying the migration...

REM Verify the schema change
docker compose exec db mysql -u root -prootpassword work_orders -e "DESCRIBE orders;"

echo.
echo 📋 Current room data after migration:
docker compose exec db mysql -u root -prootpassword work_orders -e "SELECT id, room, work_to_be_done FROM orders LIMIT 5;"

echo.
echo Migration completed! Press any key to continue...
pause >nul