package routes

import (
	"github.com/gin-gonic/gin"

	"auth-service/controllers"
	"auth-service/middleware"
)

func AuthRoutes(router *gin.Engine) {
	authGroup := router.Group("/api/auth")
	{
		authGroup.POST("/login", controllers.Login)
		authGroup.POST("/register", controllers.Register)
		authGroup.POST("/verify-token", controllers.VerifyToken)
	}

	profileGroup := router.Group("/api/profile")
	profileGroup.Use(middleware.AuthMiddleware())
	{
		profileGroup.GET("", controllers.GetProfile)
		profileGroup.PUT("", controllers.UpdateProfile)
	}

	userGroup := router.Group("/api/users")
	userGroup.Use(middleware.AuthMiddleware())
	{
		userGroup.GET("/:id", controllers.GetUser)
	}
}
