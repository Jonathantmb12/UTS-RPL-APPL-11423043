package database

import (
	"log"
	"time"

	"auth-service/config"
	"auth-service/models"
	"auth-service/utils"
)

func Seed() {
	db := config.GetDB()

	// Check if data already exists
	var count int64
	db.Model(&models.User{}).Count(&count)
	if count > 0 {
		log.Println("Database already seeded. Skipping...")
		return
	}

	// Admin User
	adminPassword, _ := utils.HashPassword("password")
	admin := models.User{
		Name:       "Admin MediTrack",
		Email:      "admin@meditrack.com",
		Password:   adminPassword,
		Role:       "admin",
		IsVerified: true,
		IsActive:   true,
	}

	// Doctors
	doctorPassword, _ := utils.HashPassword("password")
	doctors := []models.User{
		{
			Name:           "Dr. Ahmad Suryandi",
			Email:          "doctor1@meditrack.com",
			Password:       doctorPassword,
			Role:           "doctor",
			Specialization: stringPtr("Cardiology"),
			LicenseNumber:  stringPtr("DOC001"),
			HospitalName:   stringPtr("Rumah Sakit Sentosa"),
			IsVerified:     true,
			IsActive:       true,
		},
		{
			Name:           "Dr. Siti Nurhaliza",
			Email:          "doctor2@meditrack.com",
			Password:       doctorPassword,
			Role:           "doctor",
			Specialization: stringPtr("Pediatrics"),
			LicenseNumber:  stringPtr("DOC002"),
			HospitalName:   stringPtr("Rumah Sakit Sentosa"),
			IsVerified:     true,
			IsActive:       true,
		},
		{
			Name:           "Dr. Budi Santoso",
			Email:          "doctor3@meditrack.com",
			Password:       doctorPassword,
			Role:           "doctor",
			Specialization: stringPtr("Orthopedics"),
			LicenseNumber:  stringPtr("DOC003"),
			HospitalName:   stringPtr("Rumah Sakit Merdeka"),
			IsVerified:     true,
			IsActive:       true,
		},
		{
			Name:           "Dr. Dwi Handoko",
			Email:          "doctor4@meditrack.com",
			Password:       doctorPassword,
			Role:           "doctor",
			Specialization: stringPtr("Neurology"),
			LicenseNumber:  stringPtr("DOC004"),
			HospitalName:   stringPtr("Rumah Sakit Merdeka"),
			IsVerified:     true,
			IsActive:       true,
		},
	}

	// Pharmacists
	pharmacistPassword, _ := utils.HashPassword("password")
	pharmacists := []models.User{
		{
			Name:            "Apt. Rina Wijaya",
			Email:           "pharmacist1@meditrack.com",
			Password:        pharmacistPassword,
			Role:            "pharmacist",
			PharmacyName:    stringPtr("Apotek Sentosa"),
			PharmacyLicense: stringPtr("APT001"),
			IsVerified:      true,
			IsActive:        true,
		},
		{
			Name:            "Apt. Bambang Sutrisno",
			Email:           "pharmacist2@meditrack.com",
			Password:        pharmacistPassword,
			Role:            "pharmacist",
			PharmacyName:    stringPtr("Apotek Merdeka"),
			PharmacyLicense: stringPtr("APT002"),
			IsVerified:      true,
			IsActive:        true,
		},
		{
			Name:            "Apt. Citra Dewi",
			Email:           "pharmacist3@meditrack.com",
			Password:        pharmacistPassword,
			Role:            "pharmacist",
			PharmacyName:    stringPtr("Apotek Sehat"),
			PharmacyLicense: stringPtr("APT003"),
			IsVerified:      true,
			IsActive:        true,
		},
	}

	// Patients
	patientPassword, _ := utils.HashPassword("password")
	patients := []models.User{
		{
			Name:        "Adi Pratama",
			Email:       "patient1@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1990-05-10"),
			Gender:      stringPtr("male"),
			PhoneNumber: stringPtr("082123456789"),
			Address:     stringPtr("Jl. Merdeka No. 10, Jakarta"),
			BloodType:   stringPtr("O"),
			Allergies:   stringPtr("Penicillin"),
			IsVerified:  true,
			IsActive:    true,
		},
		{
			Name:        "Bina Wirawan",
			Email:       "patient2@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1985-03-22"),
			Gender:      stringPtr("female"),
			PhoneNumber: stringPtr("082234567890"),
			Address:     stringPtr("Jl. Sudirman No. 15, Jakarta"),
			BloodType:   stringPtr("A"),
			Allergies:   stringPtr(""),
			IsVerified:  true,
			IsActive:    true,
		},
		{
			Name:        "Citra Kusuma",
			Email:       "patient3@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1995-07-18"),
			Gender:      stringPtr("female"),
			PhoneNumber: stringPtr("082345678901"),
			Address:     stringPtr("Jl. Gatot Subroto No. 20, Jakarta"),
			BloodType:   stringPtr("B"),
			Allergies:   stringPtr("Aspirin"),
			IsVerified:  true,
			IsActive:    true,
		},
		{
			Name:        "Doni Setiawan",
			Email:       "patient4@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1988-11-30"),
			Gender:      stringPtr("male"),
			PhoneNumber: stringPtr("082456789012"),
			Address:     stringPtr("Jl. Ahmad Yani No. 25, Bandung"),
			BloodType:   stringPtr("AB"),
			Allergies:   stringPtr(""),
			IsVerified:  true,
			IsActive:    true,
		},
		{
			Name:        "Eka Putri",
			Email:       "patient5@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1992-09-14"),
			Gender:      stringPtr("female"),
			PhoneNumber: stringPtr("082567890123"),
			Address:     stringPtr("Jl. Siliwangi No. 30, Bandung"),
			BloodType:   stringPtr("O"),
			Allergies:   stringPtr(""),
			IsVerified:  true,
			IsActive:    true,
		},
		{
			Name:        "Farah Nanda",
			Email:       "patient6@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1994-01-08"),
			Gender:      stringPtr("female"),
			PhoneNumber: stringPtr("082678901234"),
			Address:     stringPtr("Jl. Diponegoro No. 12, Bandung"),
			BloodType:   stringPtr("A"),
			Allergies:   stringPtr("Paracetamol"),
			IsVerified:  true,
			IsActive:    true,
		},
		{
			Name:        "Gunawan Wijaya",
			Email:       "patient7@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1989-06-25"),
			Gender:      stringPtr("male"),
			PhoneNumber: stringPtr("082789012345"),
			Address:     stringPtr("Jl. Imam Bonjol No. 40, Surabaya"),
			BloodType:   stringPtr("B"),
			Allergies:   stringPtr(""),
			IsVerified:  true,
			IsActive:    true,
		},
		{
			Name:        "Heny Susanti",
			Email:       "patient8@meditrack.com",
			Password:    patientPassword,
			Role:        "patient",
			DateOfBirth: datePtr("1996-12-05"),
			Gender:      stringPtr("female"),
			PhoneNumber: stringPtr("082890123456"),
			Address:     stringPtr("Jl. Pemuda No. 50, Surabaya"),
			BloodType:   stringPtr("O"),
			Allergies:   stringPtr("Sefalosporin"),
			IsVerified:  true,
			IsActive:    true,
		},
	}

	// Save all users
	if err := db.Create(&admin).Error; err != nil {
		log.Printf("Error creating admin: %v", err)
	}

	for _, doctor := range doctors {
		if err := db.Create(&doctor).Error; err != nil {
			log.Printf("Error creating doctor: %v", err)
		}
	}

	for _, pharmacist := range pharmacists {
		if err := db.Create(&pharmacist).Error; err != nil {
			log.Printf("Error creating pharmacist: %v", err)
		}
	}

	for _, patient := range patients {
		if err := db.Create(&patient).Error; err != nil {
			log.Printf("Error creating patient: %v", err)
		}
	}

	log.Println("Seeding completed successfully!")
}

func stringPtr(s string) *string {
	return &s
}

func datePtr(s string) *time.Time {
	t, _ := time.Parse("2006-01-02", s)
	return &t
}
