package database

import (
	"log"

	"auth-service/config"
	"auth-service/models"
)

func RunMigrations() {
	err := config.GetDB().AutoMigrate(&models.User{})
	if err != nil {
		log.Fatalf("Failed to run migrations: %v", err)
	}
	log.Println("Migrations completed successfully!")
}
