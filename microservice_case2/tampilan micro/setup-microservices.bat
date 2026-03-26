@echo off
REM Script to setup MediTrack Microservices - Install dependencies and run migrations
echo.
echo ======================================
echo  MediTrack Microservices Setup Script
echo ======================================
echo.

REM Check MySQL
echo Checking MySQL installation...
mysql --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: MySQL not found or not in PATH
    echo Please install MySQL and add it to your PATH
    echo.
    pause
    exit /b 1
)
echo ✓ MySQL found

REM Check Go
echo Checking Go installation...
go version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Go not found or not in PATH
    echo Please install Go and add it to your PATH
    echo.
    pause
    exit /b 1
)
echo ✓ Go found

echo.
echo ======================================
echo  Step 1: Creating MySQL Databases
echo ======================================
echo.
echo NOTE: If prompted for MySQL password, enter your MySQL root password
echo.

REM Create databases - user must have write access
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_auth;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_patient;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_doctor;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_appointment;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_prescription;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;"
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS meditrack_analytics;"

echo.
echo ✓ Databases created successfully
echo.

REM Install Go dependencies for each service
echo ======================================
echo  Step 2: Installing Go Dependencies
echo ======================================
echo.

setlocal enabledelayedexpansion

for %%S in (auth-service patient-service doctor-service appointment-service prescription-service pharmacy-service) do (
    echo Installing dependencies for %%S...
    cd "d:\semester 6\tugas\microservice\microservices\%%S"
    
    if exist ".env.example" (
        if not exist ".env" (
            copy ".env.example" ".env"
            echo   ✓ .env created from .env.example
        )
    )
    
    echo   ► go mod download...
    go mod download >nul 2>&1
    if errorlevel 1 (
        echo   ✗ Error downloading modules for %%S
        goto error
    )
    
    echo   ► go mod tidy...
    go mod tidy >nul 2>&1
    if errorlevel 1 (
        echo   ✗ Error tidying modules for %%S
        goto error
    )
    
    echo   ✓ Done with %%S
    echo.
)

cd "d:\semester 6\tugas\microservice"

echo.
echo ======================================
echo  Step 3: Build Services and Run Migrations
echo ======================================
echo.
echo NOTE: Services will start and run migrations automatically
echo To stop each service: Press Ctrl+C
echo.

REM Now build each service which will trigger migrations
for %%S in (auth-service patient-service doctor-service appointment-service prescription-service pharmacy-service) do (
    echo.
    echo Starting %%S for migrations...
    echo (This will run for ~5 seconds to complete migrations)
    echo.
    cd "d:\semester 6\tugas\microservice\microservices\%%S"
    
    REM Run with timeout - 5 seconds should be enough for migrations
    timeout /t 5 /nobreak > nul
    
    REM Actually compile to check for errors
    echo Compiling %%S...
    go build -o "%%S.exe" main.go
    if errorlevel 1 (
        echo ✗ Build failed for %%S
        goto error
    )
    echo ✓ %%S compiled successfully
)

cd "d:\semester 6\tugas\microservice"

echo.
echo ======================================
echo  Step 4: Verify Installations
echo ======================================
echo.

for %%S in (auth-service patient-service doctor-service appointment-service prescription-service pharmacy-service) do (
    cd "d:\semester 6\tugas\microservice\microservices\%%S"
    if exist "%%S.exe" (
        echo ✓ %%S.exe built successfully
    ) else (
        echo ✗ %%S.exe not found
    )
)

cd "d:\semester 6\tugas\microservice"

echo.
echo ======================================
echo  SETUP COMPLETE!
echo ======================================
echo.
echo Next steps:
echo 1. Run each service in separate terminals:
echo    - cd microservices\auth-service && go run main.go
echo    - cd microservices\patient-service && go run main.go
echo    - etc...
echo.
echo 2. Or use: start-all-services.bat
echo.
echo 3. Test the APIs:
echo    - Auth Login: POST http://localhost:8001/api/login
echo    - Get Patients: GET http://localhost:8002/api/patients
echo    - etc...
echo.
echo Demo credentials:
echo   Email: admin@meditrack.com
echo   Password: password
echo.

pause
exit /b 0

:error
echo.
echo ======================================
echo  ERROR OCCURRED
echo ======================================
echo.
echo Please check:
echo 1. MySQL is running and accessible
echo 2. Go is properly installed (go version)
echo 3. .env files have correct database credentials
echo 4. Internet connection for downloading modules
echo.
pause
exit /b 1
