package controllers

import (
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"doctor-service/config"
	"doctor-service/models"
)

// CreateDoctor creates a new doctor
func CreateDoctor(c *gin.Context) {
	var req models.CreateDoctorRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	doctor := models.Doctor{
		UserID:         req.UserID,
		Name:           req.Name,
		Email:          req.Email,
		Specialization: req.Specialization,
		LicenseNumber:  req.LicenseNumber,
		HospitalName:   req.HospitalName,
		PhoneNumber:    req.PhoneNumber,
		Address:        req.Address,
		IsActive:       true,
	}

	if err := config.GetDB().Create(&doctor).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create doctor"})
		return
	}

	// Create performance metric
	metric := models.DoctorPerformanceMetric{
		DoctorID:               doctor.ID,
		TotalConsultations:     0,
		AvgPatientSatisfaction: 4.5,
		TotalPrescriptions:     0,
		PatientRetentionRate:   95.0,
		AverageRefusalRate:     2.5,
	}
	config.GetDB().Create(&metric)

	c.JSON(http.StatusCreated, doctor)
}

// GetDoctor retrieves a doctor by ID
func GetDoctor(c *gin.Context) {
	id := c.Param("id")

	var doctor models.Doctor
	if err := config.GetDB().Preload("PerformanceMetrics").First(&doctor, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Doctor not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, doctor)
}

// GetAllDoctors retrieves all doctors with pagination
func GetAllDoctors(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	spec := c.Query("specialization")
	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var doctors []models.Doctor
	var total int64

	query := config.GetDB()
	if spec != "" {
		query = query.Where("specialization = ?", spec)
	}

	query.Model(&models.Doctor{}).Count(&total)
	query.Offset(offset).Limit(limit).Preload("PerformanceMetrics").Find(&doctors)

	c.JSON(http.StatusOK, gin.H{
		"data":      doctors,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

// UpdateDoctor updates doctor information
func UpdateDoctor(c *gin.Context) {
	id := c.Param("id")

	var doctor models.Doctor
	if err := config.GetDB().First(&doctor, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Doctor not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	var req models.UpdateDoctorRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	if err := config.GetDB().Model(&doctor).Updates(req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update doctor"})
		return
	}

	c.JSON(http.StatusOK, doctor)
}

// DeleteDoctor deletes a doctor (soft delete)
func DeleteDoctor(c *gin.Context) {
	id := c.Param("id")

	if err := config.GetDB().Delete(&models.Doctor{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete doctor"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Doctor deleted successfully"})
}

// GetDoctorByEmail retrieves doctor by email
func GetDoctorByEmail(c *gin.Context) {
	email := c.Param("email")

	var doctor models.Doctor
	if err := config.GetDB().Where("email = ?", email).First(&doctor).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Doctor not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, doctor)
}

// SearchDoctor searches doctors by name or specialization
func SearchDoctor(c *gin.Context) {
	query := c.Query("q")

	var doctors []models.Doctor
	if err := config.GetDB().Where("name LIKE ? OR specialization LIKE ?", "%"+query+"%", "%"+query+"%").Find(&doctors).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, doctors)
}

// GetDoctorPerformance gets doctor performance metrics
func GetDoctorPerformance(c *gin.Context) {
	doctorID := c.Param("doctorerID")

	var metric models.DoctorPerformanceMetric
	if err := config.GetDB().Where("doctor_id = ?", doctorID).First(&metric).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Performance metric not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, metric)
}

// UpdatePerformanceMetric updates doctor performance
func UpdatePerformanceMetric(c *gin.Context) {
	doctorID := c.Param("doctorID")

	var metric models.DoctorPerformanceMetric
	if err := config.GetDB().Where("doctor_id = ?", doctorID).First(&metric).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Metric not found"})
		return
	}

	var updateData map[string]interface{}
	if err := c.ShouldBindJSON(&updateData); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	if err := config.GetDB().Model(&metric).Updates(updateData).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update metric"})
		return
	}

	c.JSON(http.StatusOK, metric)
}
