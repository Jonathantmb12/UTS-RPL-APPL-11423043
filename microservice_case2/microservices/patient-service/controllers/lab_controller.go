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

// CreateLabResult creates a new lab result
func CreateLabResult(c *gin.Context) {
	patientID := c.Param("patientID")

	var req models.CreateLabResultRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	patID, _ := strconv.ParseUint(patientID, 10, 32)
	testDate, _ := time.Parse("2006-01-02", req.TestDate)

	labResult := models.LabResult{
		PatientID:    uint(patID),
		TestName:     req.TestName,
		TestType:     req.TestType,
		LabName:      req.LabName,
		TestDate:     testDate,
		NormalRange:  req.NormalRange,
		Result:       req.Result,
		ResultStatus: req.ResultStatus,
		Unit:         req.Unit,
		Notes:        req.Notes,
		Status:       "pending",
	}

	if err := config.GetDB().Create(&labResult).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create lab result"})
		return
	}

	c.JSON(http.StatusCreated, labResult)
}

// GetLabResult retrieves a lab result by ID
func GetLabResult(c *gin.Context) {
	id := c.Param("id")

	var labResult models.LabResult
	if err := config.GetDB().First(&labResult, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Lab result not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, labResult)
}

// GetPatientLabResults retrieves all lab results for a patient
func GetPatientLabResults(c *gin.Context) {
	patientID := c.Param("patientID")
	page := c.DefaultQuery("page", "1")
	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var labResults []models.LabResult
	var total int64

	config.GetDB().Where("patient_id = ?", patientID).Model(&models.LabResult{}).Count(&total)
	config.GetDB().Where("patient_id = ?", patientID).Offset(offset).Limit(limit).Find(&labResults)

	c.JSON(http.StatusOK, gin.H{
		"data":      labResults,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

// UpdateLabResult updates a lab result
func UpdateLabResult(c *gin.Context) {
	id := c.Param("id")

	var labResult models.LabResult
	if err := config.GetDB().First(&labResult, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Lab result not found"})
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

	if err := config.GetDB().Model(&labResult).Updates(updateData).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update lab result"})
		return
	}

	c.JSON(http.StatusOK, labResult)
}

// DeleteLabResult deletes a lab result
func DeleteLabResult(c *gin.Context) {
	id := c.Param("id")

	if err := config.GetDB().Delete(&models.LabResult{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete lab result"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Lab result deleted successfully"})
}

// CompleteLabResult marks a lab result as completed
func CompleteLabResult(c *gin.Context) {
	id := c.Param("id")

	var labResult models.LabResult
	if err := config.GetDB().First(&labResult, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Lab result not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	now := time.Now()
	if err := config.GetDB().Model(&labResult).Updates(map[string]interface{}{
		"status":      "completed",
		"result_date": now,
	}).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to complete lab result"})
		return
	}

	c.JSON(http.StatusOK, labResult)
}
