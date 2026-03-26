#!/bin/bash

# MediTrack Microservices - Quick Start Script for Linux/Mac

echo ""
echo "============================================"
echo " MediTrack Microservices - Starting All Services"
echo "============================================"
echo ""

# Check if Go is installed
if ! command -v go &> /dev/null; then
    echo "ERROR: Go is not installed"
    echo "Please install Go from https://golang.org/"
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed"
    echo "Please install PHP to run Laravel"
    exit 1
fi

# Create databases
echo ""
echo "Creating databases..."
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS meditrack_auth;
CREATE DATABASE IF NOT EXISTS meditrack_patient;
CREATE DATABASE IF NOT EXISTS meditrack_doctor;
CREATE DATABASE IF NOT EXISTS meditrack_appointment;
CREATE DATABASE IF NOT EXISTS meditrack_prescription;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;
CREATE DATABASE IF NOT EXISTS meditrack_analytics;
EOF

# Function to start service
start_service() {
    local service=$1
    local port=$2
    echo ""
    echo "✓ Starting $service (Port $port)..."
    (cd microservices/$service && cp .env.example .env && go mod download && go run main.go) &
}

# Start all services
start_service "auth-service" "8001"
sleep 3

start_service "patient-service" "8002"
sleep 3

start_service "doctor-service" "8003"
sleep 3

start_service "appointment-service" "8004"
sleep 3

start_service "prescription-service" "8005"
sleep 3

start_service "pharmacy-service" "8006"
sleep 3

echo ""
echo "✓ Starting Laravel Development Server..."
php artisan serve &

echo ""
echo "============================================"
echo " All Services Started!"
echo "============================================"
echo ""
echo "Services running on:"
echo " - Auth Service:        http://localhost:8001"
echo " - Patient Service:     http://localhost:8002"
echo " - Doctor Service:      http://localhost:8003"
echo " - Appointment Service: http://localhost:8004"
echo " - Prescription Service: http://localhost:8005"
echo " - Pharmacy Service:    http://localhost:8006"
echo " - Laravel UI:          http://localhost:8000"
echo ""
echo "Demo Account:"
echo " Email:    admin@meditrack.com"
echo " Password: password"
echo ""
echo "Press Ctrl+C to stop all services"
echo ""

# Keep the script running
wait
