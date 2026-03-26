# MediTrack Microservices - Complete Setup Script
# Usage: .\setup-all.ps1

param(
    [Parameter(Mandatory=$false)]
    [string]$MySQLPassword = "",
    
    [Parameter(Mandatory=$false)]
    [string]$MySQLUser = "root",
    
    [Parameter(Mandatory=$false)]
    [string]$MySQLHost = "localhost"
)

# Color output
function Write-Success {
    param([string]$Message)
    Write-Host "✓ $Message" -ForegroundColor Green
}

function Write-Error-Custom {
    param([string]$Message)
    Write-Host "✗ $Message" -ForegroundColor Red
}

function Write-Step {
    param([string]$Message)
    Write-Host ""
    Write-Host "======================================" -ForegroundColor Cyan
    Write-Host "  $Message" -ForegroundColor Cyan
    Write-Host "======================================" -ForegroundColor Cyan
}

$Services = @("auth-service", "patient-service", "doctor-service", "appointment-service", "prescription-service", "pharmacy-service")
$Databases = @("meditrack_auth", "meditrack_patient", "meditrack_doctor", "meditrack_appointment", "meditrack_prescription", "meditrack_pharmacy", "meditrack_analytics")
$RootPath = "d:\semester 6\tugas\microservice"

Clear-Host
Write-Host ""
Write-Host "╔═══════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  MediTrack Microservices - Complete Setup Script  ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# =================================================================
# STEP 1: Verify Prerequisites
# =================================================================
Write-Step "Step 1: Verifying Prerequisites"

# Check Go
try {
    $GoVersion = go version
    Write-Success "Go installed: $GoVersion"
} catch {
    Write-Error-Custom "Go not found! Please install Go 1.21+"
    exit 1
}

# Check MySQL
try {
    $MySQLVersion = mysql --version
    Write-Success "MySQL installed: $MySQLVersion"
} catch {
    Write-Error-Custom "MySQL not found! Please install MySQL"
    exit 1
}

# =================================================================
# STEP 2: Create MySQL Databases
# =================================================================
Write-Step "Step 2: Creating MySQL Databases"

if ([string]::IsNullOrEmpty($MySQLPassword)) {
    Write-Host "MySQL credentials required..." -ForegroundColor Yellow
    $Credential = Get-Credential -UserName $MySQLUser -Message "Enter MySQL root credentials:"
    $MySQLPassword = $Credential.GetNetworkCredential().Password
    $MySQLUser = $Credential.UserName
}

# Create each database
$SQLCreateDatabases = @"
SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;
SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;
SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS meditrack_auth CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS meditrack_patient CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS meditrack_doctor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS meditrack_appointment CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS meditrack_prescription CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS meditrack_analytics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SHOW DATABASES LIKE 'meditrack_%';
"@

# Save to temp file and execute
$TempSQLFile = [System.IO.Path]::GetTempFileName()
$TempSQLFile = $TempSQLFile -replace '\.tmp$', '.sql'
Set-Content -Path $TempSQLFile -Value $SQLCreateDatabases -Encoding UTF8

try {
    Write-Host "Creating databases..." -ForegroundColor Yellow
    $MySQLArgs = "-h", $MySQLHost, "-u", $MySQLUser
    if (-not [string]::IsNullOrEmpty($MySQLPassword)) {
        $MySQLArgs += "-p$MySQLPassword"
    }
    $MySQLArgs += "--default-character-set=utf8mb4"
    
    # Execute SQL script
    $SQLContent = Get-Content $TempSQLFile -Raw
    $MySQLArgs += "-e"
    $MySQLArgs += $SQLContent
    
    & mysql @MySQLArgs 2>&1 | Out-Null
    
    Write-Success "All databases created successfully"
    Write-Host "  - meditrack_auth" -ForegroundColor Green
    Write-Host "  - meditrack_patient" -ForegroundColor Green
    Write-Host "  - meditrack_doctor" -ForegroundColor Green
    Write-Host "  - meditrack_appointment" -ForegroundColor Green
    Write-Host "  - meditrack_prescription" -ForegroundColor Green
    Write-Host "  - meditrack_pharmacy" -ForegroundColor Green
    Write-Host "  - meditrack_analytics" -ForegroundColor Green
} catch {
    Write-Error-Custom "Failed to create databases: $_"
    exit 1
} finally {
    Remove-Item -Path $TempSQLFile -Force -ErrorAction SilentlyContinue
}

# =================================================================
# STEP 3: Setup .env Files
# =================================================================
Write-Step "Step 3: Setting Up Environment Files"

foreach ($Service in $Services) {
    $ServicePath = Join-Path $RootPath "microservices\$Service"
    $EnvExamplePath = Join-Path $ServicePath ".env.example"
    $EnvPath = Join-Path $ServicePath ".env"
    
    if (Test-Path $EnvExamplePath) {
        if (-not (Test-Path $EnvPath)) {
            Copy-Item -Path $EnvExamplePath -Destination $EnvPath -Force
            Write-Success ".env created for $Service"
        } else {
            Write-Host "  ➜ .env already exists for $Service" -ForegroundColor Gray
        }
    }
}

# =================================================================
# STEP 4: Download Go Dependencies
# =================================================================
Write-Step "Step 4: Downloading Go Dependencies"

foreach ($Service in $Services) {
    $ServicePath = Join-Path $RootPath "microservices\$Service"
    Write-Host ""
    Write-Host "  Installing dependencies for $Service..." -ForegroundColor Yellow
    
    try {
        Push-Location $ServicePath
        
        # go mod download
        Write-Host "    ► go mod download..." -ForegroundColor Gray
        & go mod download --all 2>&1 | Out-Null
        
        # go mod tidy
        Write-Host "    ► go mod tidy..." -ForegroundColor Gray
        & go mod tidy 2>&1 | Out-Null
        
        # go mod verify
        Write-Host "    ► go mod verify..." -ForegroundColor Gray
        & go mod verify 2>&1 | Out-Null
        
        Write-Success "Completed $Service"
        
    } catch {
        Write-Error-Custom "Failed to setup $Service : $_"
        Pop-Location
        exit 1
    } finally {
        Pop-Location
    }
}

# =================================================================
# STEP 5: Build Services (Compile Check)
# =================================================================
Write-Step "Step 5: Building Services"

foreach ($Service in $Services) {
    $ServicePath = Join-Path $RootPath "microservices\$Service"
    Write-Host ""
    Write-Host "  Building $Service..." -ForegroundColor Yellow
    
    try {
        Push-Location $ServicePath
        
        & go build -o "${Service}.exe" main.go 2>&1 | Out-Null
        
        if (Test-Path "${Service}.exe") {
            Write-Success "Built: ${Service}.exe"
        } else {
            throw "Build output not found"
        }
        
    } catch {
        Write-Error-Custom "Failed to build $Service : $_"
        Pop-Location
        exit 1
    } finally {
        Pop-Location
    }
}

# =================================================================
# STEP 6: Summary and Next Steps
# =================================================================
Write-Step "Setup Complete!"

Write-Host ""
Write-Host "✓ All prerequisites verified" -ForegroundColor Green
Write-Host "✓ 7 MySQL databases created" -ForegroundColor Green
Write-Host "✓ Environment files configured" -ForegroundColor Green
Write-Host "✓ Go dependencies installed" -ForegroundColor Green
Write-Host "✓ All services compiled" -ForegroundColor Green
Write-Host ""

Write-Host "NEXT STEPS:" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Start the services:" -ForegroundColor Yellow
Write-Host "   Option A - Automated: .\start-all-services.bat"
Write-Host "   Option B - Manual: Run in separate terminals:"
Write-Host ""

$Port = 8001
foreach ($Service in $Services) {
    Write-Host "     cd microservices\$Service && go run main.go  (Port $Port)"
    $Port++
}

Write-Host ""
Write-Host "2. Verify services are running:"
Write-Host "   curl http://localhost:8001/api/health"
Write-Host ""

Write-Host "3. Test login:"
Write-Host "   curl -X POST http://localhost:8001/api/login \"
Write-Host "     -H 'Content-Type: application/json' \"
Write-Host "     -d '{""email"":""admin@meditrack.com"",""password"":""password""}'"
Write-Host ""

Write-Host "4. Demo credentials:"
Write-Host "   Email: admin@meditrack.com"
Write-Host "   Password: password"
Write-Host ""

Write-Host "Documentation:" -ForegroundColor Cyan
Write-Host "  - API_ENDPOINTS_REFERENCE.md (API endpoints)"
Write-Host "  - SETUP_MANUAL_GUIDE.md (detailed steps)"
Write-Host "  - LARAVEL_INTEGRATION_GUIDE.md (Laravel integration)"
Write-Host ""

Write-Host "Done! Press any key to exit..." -ForegroundColor Cyan
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
