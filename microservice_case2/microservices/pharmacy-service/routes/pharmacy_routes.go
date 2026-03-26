package routes

import (
	"github.com/gin-gonic/gin"

	"pharmacy-service/controllers"
	"pharmacy-service/middleware"
)

func PharmacyRoutes(router *gin.Engine) {
	pharmacyGroup := router.Group("/api/pharmacy")
	pharmacyGroup.Use(middleware.AuthMiddleware())
	{
		// Inventory routes
		pharmacyGroup.POST("/inventory", controllers.CreateInventory)
		pharmacyGroup.GET("/inventory", controllers.GetInventories)
		pharmacyGroup.GET("/inventory/:id", controllers.GetInventory)
		pharmacyGroup.PUT("/inventory/:id", controllers.UpdateInventory)
		pharmacyGroup.GET("/inventory/low-stock", controllers.GetLowStockItems)

		// Payment routes
		pharmacyGroup.POST("/payments", controllers.CreatePayment)
		pharmacyGroup.GET("/payments", controllers.GetPayments)
	}
}
