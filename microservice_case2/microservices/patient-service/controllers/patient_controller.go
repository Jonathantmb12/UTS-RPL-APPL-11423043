package controllers

import (
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"patient-service/config"
	"patient-service/models"
)

// CreatePatient creates a new patient
func CreatePatient(c *gin.Context) {
	var req models.CreatePatientRequest

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	var dateOfBirth *time.Time
	if req.DateOfBirth != nil {
		dob, err := time.Parse("2006-01-02", *req.DateOfBirth)
		if err == nil {
			dateOfBirth = &dob
		}
	}

	patient := models.Patient{
		UserID:           req.UserID,
		Name:             req.Name,
		Email:            req.Email,
		PhoneNumber:      req.PhoneNumber,
		DateOfBirth:      dateOfBirth,
		Gender:           req.Gender,
		Address:          req.Address,
		EmergencyContact: req.EmergencyContact,
		BloodType:        req.BloodType,
		Allergies:        req.Allergies,
	}

	if err := config.GetDB().Create(&patient).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create patient"})
		return
	}

	c.JSON(http.StatusCreated, patient)
}

// GetPatient retrieves a patient by ID
func GetPatient(c *gin.Context) {
	id := c.Param("id")

	var patient models.Patient
	if err := config.GetDB().Preload("EHRs").Preload("LabResults").Preload("PatientOutcomes").First(&patient, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Patient not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, patient)
}

// GetAllPatients retrieves all patients with pagination
func GetAllPatients(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var patients []models.Patient
	var total int64

	config.GetDB().Model(&models.Patient{}).Count(&total)
	config.GetDB().Offset(offset).Limit(limit).Find(&patients)

	c.JSON(http.StatusOK, gin.H{
		"data":      patients,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

// UpdatePatient updates patient information
func UpdatePatient(c *gin.Context) {
	id := c.Param("id")

	var patient models.Patient
	if err := config.GetDB().First(&patient, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Patient not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	var updateData map[string]interface{}
	if err := c.ShouldBindJSON(&updateData); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	if err := config.GetDB().Model(&patient).Updates(updateData).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update patient"})
		return
	}

	c.JSON(http.StatusOK, patient)
}

// DeletePatient deletes a patient (soft delete)
func DeletePatient(c *gin.Context) {
	id := c.Param("id")

	if err := config.GetDB().Delete(&models.Patient{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete patient"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Patient deleted successfully"})
}

// GetPatientByEmail retrieves patient by email
func GetPatientByEmail(c *gin.Context) {
	email := c.Param("email")

	var patient models.Patient
	if err := config.GetDB().Where("email = ?", email).First(&patient).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Patient not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, patient)
}

// SearchPatient searches patients by name or other fields
func SearchPatient(c *gin.Context) {
	query := c.Query("q")

	var patients []models.Patient
	if err := config.GetDB().Where("name LIKE ? OR email LIKE ? OR phone_number LIKE ?", "%"+query+"%", "%"+query+"%", "%"+query+"%").Find(&patients).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, patients)
}
