@echo off
REM MediTrack Microservices - Quick Start Script for Windows

echo.
echo ============================================
echo  MediTrack Microservices - Starting All Services
echo ============================================
echo.

REM Check if Go is installed
where go >nul 2>nul
if errorlevel 1 (
    echo ERROR: Go is not installed or not in PATH
    echo Please install Go from https://golang.org/
    pause
    exit /b 1
)

REM Check if PHP is installed
where php >nul 2>nul
if errorlevel 1 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP to run Laravel
    pause
    exit /b 1
)

REM Create databases
echo.
echo Creating databases...
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_auth;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_patient;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_doctor;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_appointment;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_prescription;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_analytics;"

echo.
echo ✓ Starting Auth Service (Port 8001)...
start cmd /k "cd microservices\auth-service && copy .env.example .env && go mod download && go run main.go"

timeout /t 3 /nobreak

echo ✓ Starting Patient Service (Port 8002)...
start cmd /k "cd microservices\patient-service && copy .env.example .env && go mod download && go run main.go"

timeout /t 3 /nobreak

echo ✓ Starting Doctor Service (Port 8003)...
start cmd /k "cd microservices\doctor-service && copy .env.example .env && go mod download && go run main.go"

timeout /t 3 /nobreak

echo ✓ Starting Appointment Service (Port 8004)...
start cmd /k "cd microservices\appointment-service && copy .env.example .env && go mod download && go run main.go"

timeout /t 3 /nobreak

echo ✓ Starting Prescription Service (Port 8005)...
start cmd /k "cd microservices\prescription-service && copy .env.example .env && go mod download && go run main.go"

timeout /t 3 /nobreak

echo ✓ Starting Pharmacy Service (Port 8006)...
start cmd /k "cd microservices\pharmacy-service && copy .env.example .env && go mod download && go run main.go"

timeout /t 3 /nobreak

echo.
echo ✓ Starting Laravel Development Server...
start cmd /k "php artisan serve"

echo.
echo ============================================
echo  All Services Started! 
echo ============================================
echo.
echo Services running on:
echo  - Auth Service:        http://localhost:8001
echo  - Patient Service:     http://localhost:8002
echo  - Doctor Service:      http://localhost:8003
echo  - Appointment Service: http://localhost:8004
echo  - Prescription Service: http://localhost:8005
echo  - Pharmacy Service:    http://localhost:8006
echo  - Laravel UI:          http://localhost:8000
echo.
echo Demo Account:
echo  Email:    admin@meditrack.com
echo  Password: password
echo.
echo Press any key to close this window...
pause
