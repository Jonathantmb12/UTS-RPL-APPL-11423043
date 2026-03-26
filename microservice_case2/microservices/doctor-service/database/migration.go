package database

import (
	"log"

	"doctor-service/config"
	"doctor-service/models"
)

func RunMigrations() {
	err := config.GetDB().AutoMigrate(
		&models.Doctor{},
		&models.DoctorPerformanceMetric{},
	)
	if err != nil {
		log.Fatalf("Failed to run migrations: %v", err)
	}
	log.Println("Doctor Service Migrations completed successfully!")
}

func Seed() {
	db := config.GetDB()

	// Check if data already exists
	var count int64
	db.Model(&models.Doctor{}).Count(&count)
	if count > 0 {
		log.Println("Doctor database already seeded. Skipping...")
		return
	}

	// Seed doctors (referencing User IDs from Auth Service)
	doctors := []models.Doctor{
		{
			UserID:         2, // Dr. Ahmad Suryandi
			Name:           "Dr. Ahmad Suryandi",
			Email:          "doctor1@meditrack.com",
			Specialization: "Cardiology",
			LicenseNumber:  "DOC001",
			HospitalName:   "Rumah Sakit Sentosa",
			IsActive:       true,
		},
		{
			UserID:         3, // Dr. Siti Nurhaliza
			Name:           "Dr. Siti Nurhaliza",
			Email:          "doctor2@meditrack.com",
			Specialization: "Pediatrics",
			LicenseNumber:  "DOC002",
			HospitalName:   "Rumah Sakit Sentosa",
			IsActive:       true,
		},
		{
			UserID:         4, // Dr. Budi Santoso
			Name:           "Dr. Budi Santoso",
			Email:          "doctor3@meditrack.com",
			Specialization: "Orthopedics",
			LicenseNumber:  "DOC003",
			HospitalName:   "Rumah Sakit Merdeka",
			IsActive:       true,
		},
		{
			UserID:         5, // Dr. Dwi Handoko
			Name:           "Dr. Dwi Handoko",
			Email:          "doctor4@meditrack.com",
			Specialization: "Neurology",
			LicenseNumber:  "DOC004",
			HospitalName:   "Rumah Sakit Merdeka",
			IsActive:       true,
		},
	}

	for _, doctor := range doctors {
		if err := db.Create(&doctor).Error; err != nil {
			log.Printf("Error creating doctor: %v", err)
		}

		// Create performance metric for each doctor
		metric := models.DoctorPerformanceMetric{
			DoctorID:               doctor.ID,
			TotalConsultations:     0,
			AvgPatientSatisfaction: 4.5,
			TotalPrescriptions:     0,
			PatientRetentionRate:   95.0,
			AverageRefusalRate:     2.5,
			PendingApprovals:       0,
			CompletedApprovals:     0,
			RejectedApprovals:      0,
		}
		db.Create(&metric)
	}

	log.Println("Doctor Service Seeding completed successfully!")
}
