package database

import (
	"log"
	"time"

	"patient-service/config"
	"patient-service/models"
)

func RunMigrations() {
	err := config.GetDB().AutoMigrate(
		&models.Patient{},
		&models.ElectronicHealthRecord{},
		&models.LabResult{},
		&models.PatientOutcome{},
	)
	if err != nil {
		log.Fatalf("Failed to run migrations: %v", err)
	}
	log.Println("Patient Service Migrations completed successfully!")
}

func Seed() {
	db := config.GetDB()

	// Check if data already exists
	var count int64
	db.Model(&models.Patient{}).Count(&count)
	if count > 0 {
		log.Println("Patient database already seeded. Skipping...")
		return
	}

	// Seed patients (referencing User IDs from Auth Service)
	patients := []models.Patient{
		{
			UserID:           8, // Adi Pratama
			Name:             "Adi Pratama",
			Email:            "patient1@meditrack.com",
			PhoneNumber:      "082123456789",
			DateOfBirth:      datePtr("1990-05-10"),
			Gender:           "male",
			Address:          "Jl. Merdeka No. 10, Jakarta",
			EmergencyContact: "Putri Pratama",
			BloodType:        "O",
			Allergies:        "Penicillin",
		},
		{
			UserID:           9, // Bina Wirawan
			Name:             "Bina Wirawan",
			Email:            "patient2@meditrack.com",
			PhoneNumber:      "082234567890",
			DateOfBirth:      datePtr("1985-03-22"),
			Gender:           "female",
			Address:          "Jl. Sudirman No. 15, Jakarta",
			EmergencyContact: "Ahmad Wirawan",
			BloodType:        "A",
			Allergies:        "",
		},
		{
			UserID:           10, // Citra Kusuma
			Name:             "Citra Kusuma",
			Email:            "patient3@meditrack.com",
			PhoneNumber:      "082345678901",
			DateOfBirth:      datePtr("1995-07-18"),
			Gender:           "female",
			Address:          "Jl. Gatot Subroto No. 20, Jakarta",
			EmergencyContact: "Rini Kusuma",
			BloodType:        "B",
			Allergies:        "Aspirin",
		},
		{
			UserID:           11, // Doni Setiawan
			Name:             "Doni Setiawan",
			Email:            "patient4@meditrack.com",
			PhoneNumber:      "082456789012",
			DateOfBirth:      datePtr("1988-11-30"),
			Gender:           "male",
			Address:          "Jl. Ahmad Yani No. 25, Bandung",
			EmergencyContact: "Siti Setiawan",
			BloodType:        "AB",
			Allergies:        "",
		},
		{
			UserID:           12, // Eka Putri
			Name:             "Eka Putri",
			Email:            "patient5@meditrack.com",
			PhoneNumber:      "082567890123",
			DateOfBirth:      datePtr("1992-09-14"),
			Gender:           "female",
			Address:          "Jl. Siliwangi No. 30, Bandung",
			EmergencyContact: "Budi Putri",
			BloodType:        "O",
			Allergies:        "",
		},
		{
			UserID:           13, // Farah Nanda
			Name:             "Farah Nanda",
			Email:            "patient6@meditrack.com",
			PhoneNumber:      "082678901234",
			DateOfBirth:      datePtr("1994-01-08"),
			Gender:           "female",
			Address:          "Jl. Diponegoro No. 12, Bandung",
			EmergencyContact: "Noor Nanda",
			BloodType:        "A",
			Allergies:        "Paracetamol",
		},
		{
			UserID:           14, // Gunawan Wijaya
			Name:             "Gunawan Wijaya",
			Email:            "patient7@meditrack.com",
			PhoneNumber:      "082789012345",
			DateOfBirth:      datePtr("1989-06-25"),
			Gender:           "male",
			Address:          "Jl. Imam Bonjol No. 40, Surabaya",
			EmergencyContact: "Wijaya Gunawan",
			BloodType:        "B",
			Allergies:        "",
		},
		{
			UserID:           15, // Heny Susanti
			Name:             "Heny Susanti",
			Email:            "patient8@meditrack.com",
			PhoneNumber:      "082890123456",
			DateOfBirth:      datePtr("1996-12-05"),
			Gender:           "female",
			Address:          "Jl. Pemuda No. 50, Surabaya",
			EmergencyContact: "Susanti Yuda",
			BloodType:        "O",
			Allergies:        "Sefalosporin",
		},
	}

	for _, patient := range patients {
		if err := db.Create(&patient).Error; err != nil {
			log.Printf("Error creating patient: %v", err)
		}
	}

	// Seed EHR records
	ehrs := []models.ElectronicHealthRecord{
		{
			PatientID:          1,
			RecordDate:         time.Now(),
			MedicalHistory:     "Hipertensi, Diabetes tipe 2",
			CurrentMedications: "Lisinopril 10mg, Metformin 500mg",
			SurgicalHistory:    "Appendectomy 2010",
			FamilyHistory:      "Hipertensi (ibu), Diabetes (bapak)",
			Vaccinations:       "Lengkap sesuai jadwal",
		},
		{
			PatientID:          2,
			RecordDate:         time.Now(),
			MedicalHistory:     "Alergi rhinitis, Asthma",
			CurrentMedications: "Cetirizine 10mg, Salbutamol inhaler",
			SurgicalHistory:    "None",
			FamilyHistory:      "Asthma (ibu)",
			Vaccinations:       "Lengkap",
		},
	}

	for _, ehr := range ehrs {
		if err := db.Create(&ehr).Error; err != nil {
			log.Printf("Error creating EHR: %v", err)
		}
	}

	// Seed lab results
	labResults := []models.LabResult{
		{
			PatientID:    1,
			TestName:     "Blood Glucose",
			TestType:     "Blood Test",
			LabName:      "Lab Sentosa",
			TestDate:     time.Now().AddDate(0, -1, 0),
			ResultDate:   dateTimePtr(time.Now().AddDate(0, -1, 2)),
			NormalRange:  "70-100 mg/dL",
			Result:       "145",
			ResultStatus: "abnormal",
			Unit:         "mg/dL",
			Notes:        "Slightly elevated, monitor diet",
			Status:       "completed",
		},
	}

	for _, labResult := range labResults {
		if err := db.Create(&labResult).Error; err != nil {
			log.Printf("Error creating lab result: %v", err)
		}
	}

	log.Println("Patient Service Seeding completed successfully!")
}

func datePtr(s string) *time.Time {
	t, _ := time.Parse("2006-01-02", s)
	return &t
}

func dateTimePtr(t time.Time) *time.Time {
	return &t
}
