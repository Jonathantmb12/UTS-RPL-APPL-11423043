package database

import (
	"log"
	"time"

	"pharmacy-service/config"
	"pharmacy-service/models"
)

func RunMigrations() {
	err := config.GetDB().AutoMigrate(
		&models.PharmacyInventory{},
		&models.Payment{},
	)
	if err != nil {
		log.Fatalf("Failed to run migrations: %v", err)
	}
	log.Println("Pharmacy Service Migrations completed successfully!")
}

func Seed() {
	db := config.GetDB()

	var count int64
	db.Model(&models.PharmacyInventory{}).Count(&count)
	if count > 0 {
		log.Println("Pharmacy database already seeded. Skipping...")
		return
	}

	// Seed inventory
	inventories := []models.PharmacyInventory{
		{
			PharmacyID:      1,
			MedicationName:  "Lisinopril",
			GenericName:     "Lisinopril",
			SKU:             "LISI-10-30",
			StockQuantity:   100,
			ReorderLevel:    20,
			ReorderQuantity: 50,
			UnitPrice:       2.50,
			BatchNumber:     "BATCH001",
			ExpirationDate:  time.Now().AddDate(1, 0, 0),
			Manufacturer:    "Generic Pharma",
			Description:     "ACE Inhibitor for hypertension",
			IsActive:        true,
		},
	}

	for _, inv := range inventories {
		if err := db.Create(&inv).Error; err != nil {
			log.Printf("Error creating inventory: %v", err)
		}
	}

	log.Println("Pharmacy Service Seeding completed successfully!")
}
