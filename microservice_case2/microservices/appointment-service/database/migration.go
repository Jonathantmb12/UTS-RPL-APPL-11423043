package database

import (
	"log"
	"time"

	"appointment-service/config"
	"appointment-service/models"
)

func RunMigrations() {
	err := config.GetDB().AutoMigrate(&models.Appointment{})
	if err != nil {
		log.Fatalf("Failed to run migrations: %v", err)
	}
	log.Println("Appointment Service Migrations completed successfully!")
}

func Seed() {
	db := config.GetDB()

	var count int64
	db.Model(&models.Appointment{}).Count(&count)
	if count > 0 {
		log.Println("Appointment database already seeded. Skipping...")
		return
	}

	// Sample appointments
	appointments := []models.Appointment{
		{
			PatientID:        1,
			DoctorID:         1,
			AppointmentDate:  time.Now().AddDate(0, 0, 7),
			DurationMinutes:  30,
			Status:           "scheduled",
			ReasonForVisit:   "Routine Checkup",
			ConsultationType: "in-person",
		},
		{
			PatientID:        2,
			DoctorID:         2,
			AppointmentDate:  time.Now().AddDate(0, 0, 5),
			DurationMinutes:  45,
			Status:           "scheduled",
			ReasonForVisit:   "Pediatric Consultation",
			ConsultationType: "in-person",
		},
	}

	for _, apt := range appointments {
		if err := db.Create(&apt).Error; err != nil {
			log.Printf("Error creating appointment: %v", err)
		}
	}

	log.Println("Appointment Service Seeding completed successfully!")
}
