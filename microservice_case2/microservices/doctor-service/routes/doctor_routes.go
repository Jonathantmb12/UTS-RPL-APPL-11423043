package routes

import (
	"github.com/gin-gonic/gin"

	"doctor-service/controllers"
	"doctor-service/middleware"
)

func DoctorRoutes(router *gin.Engine) {
	doctorGroup := router.Group("/api/doctors")
	doctorGroup.Use(middleware.AuthMiddleware())
	{
		doctorGroup.POST("", controllers.CreateDoctor)
		doctorGroup.GET("", controllers.GetAllDoctors)
		doctorGroup.GET("/:id", controllers.GetDoctor)
		doctorGroup.PUT("/:id", controllers.UpdateDoctor)
		doctorGroup.DELETE("/:id", controllers.DeleteDoctor)
		doctorGroup.GET("/email/:email", controllers.GetDoctorByEmail)
		doctorGroup.GET("/search", controllers.SearchDoctor)

		// Performance metrics
		doctorGroup.GET("/:doctorID/performance", controllers.GetDoctorPerformance)
		doctorGroup.PUT("/:doctorID/performance", controllers.UpdatePerformanceMetric)
	}
}
