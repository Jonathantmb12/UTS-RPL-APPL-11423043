package main

import (
	"log"
	"os"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"

	"patient-service/config"
	"patient-service/database"
	"patient-service/middleware"
	"patient-service/routes"
)

func init() {
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found")
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

	// Apply middleware
	router.Use(middleware.CORSMiddleware())

	// Setup routes
	routes.PatientRoutes(router)

	// Health check endpoint
	router.GET("/health", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"status": "Patient Service is running",
		})
	})

	// Start server
	port := os.Getenv("PORT")
	if port == "" {
		port = "8002"
	}

	log.Printf("Patient Service starting on port %s...", port)
	if err := router.Run(":" + port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
