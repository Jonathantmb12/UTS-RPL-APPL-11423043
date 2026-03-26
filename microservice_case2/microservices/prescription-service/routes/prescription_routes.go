package routes

import (
	"github.com/gin-gonic/gin"

	"prescription-service/controllers"
	"prescription-service/middleware"
)

func PrescriptionRoutes(router *gin.Engine) {
	prescriptionGroup := router.Group("/api/prescriptions")
	prescriptionGroup.Use(middleware.AuthMiddleware())
	{
		prescriptionGroup.POST("", controllers.CreatePrescription)
		prescriptionGroup.GET("", controllers.GetPrescriptions)
		prescriptionGroup.GET("/:id", controllers.GetPrescription)
		prescriptionGroup.PUT("/:id", controllers.UpdatePrescription)

		// Prescription Orders
		prescriptionGroup.POST("/:prescriptionID/orders", controllers.CreatePrescriptionOrder)
		prescriptionGroup.GET("/:prescriptionID/orders", controllers.GetPrescriptionOrders)
	}
}
