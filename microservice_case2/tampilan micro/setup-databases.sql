-- Create all required databases for MediTrack Microservices
CREATE DATABASE IF NOT EXISTS meditrack_auth;
CREATE DATABASE IF NOT EXISTS meditrack_patient;
CREATE DATABASE IF NOT EXISTS meditrack_doctor;
CREATE DATABASE IF NOT EXISTS meditrack_appointment;
CREATE DATABASE IF NOT EXISTS meditrack_prescription;
CREATE DATABASE IF NOT EXISTS meditrack_pharmacy;
CREATE DATABASE IF NOT EXISTS meditrack_analytics;

-- Show created databases
SHOW DATABASES LIKE 'meditrack_%';
