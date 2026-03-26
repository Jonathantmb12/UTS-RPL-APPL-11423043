package routes

import (
	"github.com/gin-gonic/gin"

	"appointment-service/controllers"
	"appointment-service/middleware"
)

func AppointmentRoutes(router *gin.Engine) {
	appointmentGroup := router.Group("/api/appointments")
	appointmentGroup.Use(middleware.AuthMiddleware())
	{
		appointmentGroup.POST("", controllers.CreateAppointment)
		appointmentGroup.GET("", controllers.GetAppointments)
		appointmentGroup.GET("/:id", controllers.GetAppointment)
		appointmentGroup.PUT("/:id", controllers.UpdateAppointment)
		appointmentGroup.POST("/:id/cancel", controllers.CancelAppointment)
		appointmentGroup.GET("/available-slots", controllers.GetAvailableSlots)
	}
}
