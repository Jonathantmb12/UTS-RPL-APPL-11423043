package database

import (
	"log"
	"time"

	"prescription-service/config"
	"prescription-service/models"
)

func RunMigrations() {
	err := config.GetDB().AutoMigrate(
		&models.Prescription{},
		&models.PrescriptionOrder{},
	)
	if err != nil {
		log.Fatalf("Failed to run migrations: %v", err)
	}
	log.Println("Prescription Service Migrations completed successfully!")
}

func Seed() {
	db := config.GetDB()

	var count int64
	db.Model(&models.Prescription{}).Count(&count)
	if count > 0 {
		log.Println("Prescription database already seeded. Skipping...")
		return
	}

	now := time.Now()
	prescriptions := []models.Prescription{
		{
			PatientID:          1,
			DoctorID:           1,
			MedicationName:     "Lisinopril",
			Description:        "ACE Inhibitor for hypertension",
			Dosage:             "10mg",
			Frequency:          "Once daily",
			Quantity:           30,
			DurationDays:       30,
			Instructions:       "Take in the morning",
			SideEffectsWarning: "May cause dizziness",
			Status:             "active",
			PrescribedDate:     now,
			ExpirationDate:     now.AddDate(0, 1, 0),
		},
	}

	for _, presc := range prescriptions {
		if err := db.Create(&presc).Error; err != nil {
			log.Printf("Error creating prescription: %v", err)
		}
	}

	log.Println("Prescription Service Seeding completed successfully!")
}
