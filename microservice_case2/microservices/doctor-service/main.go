package main

import (
	"log"
	"os"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"

	"doctor-service/config"
	"doctor-service/database"
	"doctor-service/middleware"
	"doctor-service/routes"
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

	routes.DoctorRoutes(router)

	router.GET("/health", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"status": "Doctor Service is running",
		})
	})

	port := os.Getenv("PORT")
	if port == "" {
		port = "8003"
	}

	log.Printf("Doctor Service starting on port %s...", port)
	if err := router.Run(":" + port); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
