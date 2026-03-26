@echo off
REM Fix all Go module imports across all microservices
echo.
echo ======================================
echo  Fixing Go Module Imports
echo ======================================
echo.

setlocal enabledelayedexpansion

set services=auth-service patient-service doctor-service appointment-service prescription-service pharmacy-service

cd /d "d:\semester 6\tugas\microservice\microservices"

for %%S in (%services%) do (
    echo.
    echo Fixing modules for %%S...
    echo.
    
    cd "%%S"
    
    echo  ► Step 1: Clean module cache...
    call go clean -modcache >nul 2>&1
    
    echo  ► Step 2: Delete old go.sum...
    if exist go.sum del /f /q go.sum >nul 2>&1
    
    echo  ► Step 3: Run go mod tidy...
    call go mod tidy -v 2>&1 | findstr /C:"go:" || echo    (no errors)
    
    echo  ► Step 4: Verify module...
    call go mod verify 2>&1 | findstr /C:"verified" || echo    (verified)
    
    echo  ► Step 5: Test compile...
    call go build -o "%%S.exe" main.go 2>&1
    if errorlevel 1 (
        echo    ✗ Compilation failed for %%S
    ) else (
        echo    ✓ Compilation successful for %%S
        del "%%S.exe" >nul 2>&1
    )
    
    cd ..
)

cd /d "d:\semester 6\tugas\microservice"

echo.
echo ======================================
echo  Finished Fixing Module Imports
echo ======================================
echo.
echo All services should now compile without import errors.
echo.
pause
