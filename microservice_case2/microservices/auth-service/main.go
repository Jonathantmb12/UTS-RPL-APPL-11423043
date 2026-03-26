package main

import (
	"log"
	"os"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"

	"auth-service/config"
	"auth-service/database"
	"auth-service/middleware"
	"auth-service/routes"
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

	// Apply middleware
	router.Use(middleware.CORSMiddleware())

	// Setup routes
	routes.AuthRoutes(router)

	// Health check endpoint
	router.GET("/health", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"status": "Auth Service is running",
		})
	})

	// Start server
	port := os.Getenv("PORT")
	if port == "" {
		port = "8001"
	}

	log.Printf("Auth Service starting on port %s...", port)
	if err := router.Run(":" + port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
