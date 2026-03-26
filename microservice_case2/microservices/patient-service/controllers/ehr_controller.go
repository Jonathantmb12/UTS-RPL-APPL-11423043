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

// CreateEHR creates electronic health record
func CreateEHR(c *gin.Context) {
	patientID := c.Param("patientID")

	var req models.CreateEHRRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	patID, _ := strconv.ParseUint(patientID, 10, 32)

	ehr := models.ElectronicHealthRecord{
		PatientID:          uint(patID),
		RecordDate:         time.Now(),
		MedicalHistory:     req.MedicalHistory,
		CurrentMedications: req.CurrentMedications,
		SurgicalHistory:    req.SurgicalHistory,
		FamilyHistory:      req.FamilyHistory,
		Vaccinations:       req.Vaccinations,
	}

	if err := config.GetDB().Create(&ehr).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create EHR"})
		return
	}

	c.JSON(http.StatusCreated, ehr)
}

// GetEHR retrieves EHR by ID
func GetEHR(c *gin.Context) {
	id := c.Param("id")

	var ehr models.ElectronicHealthRecord
	if err := config.GetDB().First(&ehr, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "EHR not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, ehr)
}

// GetPatientEHRs retrieves all EHRs for a patient
func GetPatientEHRs(c *gin.Context) {
	patientID := c.Param("patientID")
	page := c.DefaultQuery("page", "1")
	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var ehrs []models.ElectronicHealthRecord
	var total int64

	config.GetDB().Where("patient_id = ?", patientID).Model(&models.ElectronicHealthRecord{}).Count(&total)
	config.GetDB().Where("patient_id = ?", patientID).Offset(offset).Limit(limit).Find(&ehrs)

	c.JSON(http.StatusOK, gin.H{
		"data":      ehrs,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

// UpdateEHR updates EHR
func UpdateEHR(c *gin.Context) {
	id := c.Param("id")

	var ehr models.ElectronicHealthRecord
	if err := config.GetDB().First(&ehr, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "EHR not found"})
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

	if err := config.GetDB().Model(&ehr).Updates(updateData).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update EHR"})
		return
	}

	c.JSON(http.StatusOK, ehr)
}

// DeleteEHR deletes EHR
func DeleteEHR(c *gin.Context) {
	id := c.Param("id")

	if err := config.GetDB().Delete(&models.ElectronicHealthRecord{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete EHR"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "EHR deleted successfully"})
}
