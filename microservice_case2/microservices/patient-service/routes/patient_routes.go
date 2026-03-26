package routes

import (
	"github.com/gin-gonic/gin"

	"patient-service/controllers"
	"patient-service/middleware"
)

func PatientRoutes(router *gin.Engine) {
	// Patient routes
	patientGroup := router.Group("/api/patients")
	patientGroup.Use(middleware.AuthMiddleware())
	{
		patientGroup.POST("", controllers.CreatePatient)
		patientGroup.GET("", controllers.GetAllPatients)
		patientGroup.GET("/:id", controllers.GetPatient)
		patientGroup.PUT("/:id", controllers.UpdatePatient)
		patientGroup.DELETE("/:id", controllers.DeletePatient)
		patientGroup.GET("/email/:email", controllers.GetPatientByEmail)
		patientGroup.GET("/search", controllers.SearchPatient)

		// EHR routes
		patientGroup.POST("/:patientID/ehr", controllers.CreateEHR)
		patientGroup.GET("/:patientID/ehr", controllers.GetPatientEHRs)
		patientGroup.PUT("/ehr/:id", controllers.UpdateEHR)
		patientGroup.DELETE("/ehr/:id", controllers.DeleteEHR)
		patientGroup.GET("/ehr/:id", controllers.GetEHR)

		// Lab Result routes
		patientGroup.POST("/:patientID/lab-results", controllers.CreateLabResult)
		patientGroup.GET("/:patientID/lab-results", controllers.GetPatientLabResults)
		patientGroup.GET("/lab-results/:id", controllers.GetLabResult)
		patientGroup.PUT("/lab-results/:id", controllers.UpdateLabResult)
		patientGroup.DELETE("/lab-results/:id", controllers.DeleteLabResult)
		patientGroup.POST("/lab-results/:id/complete", controllers.CompleteLabResult)
	}
}
