package main

import (
	"log"
	"os"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"

	"analytics-service/config"
	"analytics-service/database"
	"analytics-service/routes"
)

func init() {
	// Load environment variables
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found, using system environment variables")
	}
}

func main() {
	// Connect to database
	config.ConnectDatabase()

	// Run migrations
	database.RunMigrations()

	// Seed database
	database.Seed()

	// Create gin router
	router := gin.Default()

	// Setup routes
	routes.AnalyticsRoutes(router)

	// Get port from environment
	port := os.Getenv("PORT")
	if port == "" {
		port = "8007"
	}

	log.Printf("Analytics Service running on :%s\n", port)
	router.Run(":" + port)
}
