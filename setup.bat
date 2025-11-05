@echo off
echo ========================================
echo Smart Expense Dashboard - Setup Script
echo ========================================
echo.

:: Check if XAMPP is installed
if not exist "C:\xampp" (
    echo ERROR: XAMPP not found at C:\xampp
    echo Please install XAMPP first!
    pause
    exit /b
)

echo Copying project to XAMPP htdocs...
xcopy /E /I /Y "%~dp0" "C:\xampp\htdocs\Xpense"

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo NEXT STEPS:
echo 1. Start XAMPP Control Panel
echo 2. Start Apache and MySQL services
echo 3. Open: http://localhost/phpmyadmin
echo 4. Create database 'expense_tracker'
echo 5. Import: C:\xampp\htdocs\Xpense\database\schema.sql
echo 6. Open: http://localhost/Xpense/public/login.php
echo.
echo Default Login:
echo    Username: admin
echo    Password: admin123
echo.
echo ========================================
pause
