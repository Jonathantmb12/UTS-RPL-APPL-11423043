package main

import (
	"log"
	"os"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"

	"appointment-service/config"
	"appointment-service/database"
	"appointment-service/middleware"
	"appointment-service/routes"
)

func init() {
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found")
	}
}

func main() {
	config.ConnectDatabase()
	database.RunMigrations()
	database.Seed()

	router := gin.Default()
	router.Use(middleware.CORSMiddleware())

	routes.AppointmentRoutes(router)

	router.GET("/health", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"status": "Appointment Service is running",
		})
	})

	port := os.Getenv("PORT")
	if port == "" {
		port = "8004"
	}

	log.Printf("Appointment Service starting on port %s...", port)
	if err := router.Run(":" + port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
