# Script to create all required databases for MediTrack Microservices
# Usage: .\create-databases.ps1 [mysql_password]

param(
    [Parameter(Position=0)]
    [string]$MySQLPassword = "",
    
    [Parameter(Position=1)]
    [string]$MySQLUser = "root",
    
    [Parameter(Position=2)]
    [string]$MySQLHost = "localhost"
)

# SQL to create databases
$SQL = @"
CREATE DATABASE IF NOT EXISTS meditrack_auth;
CREATE DATABASE IF NOT EXISTS meditrack_patient;
CREATE DATABASE IF NOT EXISTS meditrack_doctor;
CREATE DATABASE IF NOT EXISTS meditrack_appointment;
CREATE DATABASE IF NOT EXISTS meditrack_prescription;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;
CREATE DATABASE IF NOT EXISTS meditrack_analytics;
SHOW DATABASES LIKE 'meditrack_%';
"@

Write-Host "======================================"
Write-Host "Creating MediTrack Microservices Databases"
Write-Host "======================================"
Write-Host "MySQL Host: $MySQLHost"
Write-Host "MySQL User: $MySQLUser"
Write-Host ""

# If no password provided, prompt for it
if ([string]::IsNullOrEmpty($MySQLPassword)) {
    $Credential = Get-Credential -UserName $MySQLUser -Message "Enter MySQL credentials:"
    $MySQLPassword = $Credential.GetNetworkCredential().Password
}

# Save SQL to temp file
$TempFile = [System.IO.Path]::GetTempFileName()
Set-Content -Path $TempFile -Value $SQL

# Execute MySQL command
try {
    Write-Host "Creating databases..."
    if ($MySQLPassword) {
        mysql -h $MySQLHost -u $MySQLUser -p"$MySQLPassword" < $TempFile
    } else {
        mysql -h $MySQLHost -u $MySQLUser < $TempFile
    }
    
    Write-Host ""
    Write-Host "✓ Databases created successfully!"
    Write-Host ""
    Write-Host "Databases created:"
    Write-Host "  - meditrack_auth"
    Write-Host "  - meditrack_patient"
    Write-Host "  - meditrack_doctor"
    Write-Host "  - meditrack_appointment"
    Write-Host "  - meditrack_prescription"
    Write-Host "  - meditrack_pharmacy"
    Write-Host "  - meditrack_analytics"
} catch {
    Write-Host "✗ Error creating databases: $_"
    exit 1
} finally {
    # Clean up temp file
    Remove-Item -Path $TempFile -Force -ErrorAction SilentlyContinue
}
