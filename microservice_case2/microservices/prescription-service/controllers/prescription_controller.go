package controllers

import (
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"prescription-service/config"
	"prescription-service/models"
)

func CreatePrescription(c *gin.Context) {
	var req models.CreatePrescriptionRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	now := time.Now()
	prescription := models.Prescription{
		PatientID:          req.PatientID,
		DoctorID:           req.DoctorID,
		AppointmentID:      req.AppointmentID,
		MedicationName:     req.MedicationName,
		Description:        req.Description,
		Dosage:             req.Dosage,
		Frequency:          req.Frequency,
		Quantity:           req.Quantity,
		DurationDays:       req.DurationDays,
		Instructions:       req.Instructions,
		SideEffectsWarning: req.SideEffectsWarning,
		Status:             "active",
		PrescribedDate:     now,
		ExpirationDate:     now.AddDate(0, 0, req.DurationDays),
	}

	if err := config.GetDB().Create(&prescription).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create prescription"})
		return
	}

	c.JSON(http.StatusCreated, prescription)
}

func GetPrescription(c *gin.Context) {
	id := c.Param("id")

	var prescription models.Prescription
	if err := config.GetDB().Preload("Orders").First(&prescription, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Prescription not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, prescription)
}

func GetPrescriptions(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	patientID := c.Query("patient_id")
	doctorID := c.Query("doctor_id")
	status := c.Query("status")

	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var prescriptions []models.Prescription
	var total int64

	query := config.GetDB()
	if patientID != "" {
		query = query.Where("patient_id = ?", patientID)
	}
	if doctorID != "" {
		query = query.Where("doctor_id = ?", doctorID)
	}
	if status != "" {
		query = query.Where("status = ?", status)
	}

	query.Model(&models.Prescription{}).Count(&total)
	query.Offset(offset).Limit(limit).Preload("Orders").Find(&prescriptions)

	c.JSON(http.StatusOK, gin.H{
		"data":      prescriptions,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

func UpdatePrescription(c *gin.Context) {
	id := c.Param("id")

	var prescription models.Prescription
	if err := config.GetDB().First(&prescription, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Prescription not found"})
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

	if err := config.GetDB().Model(&prescription).Updates(updateData).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update prescription"})
		return
	}

	c.JSON(http.StatusOK, prescription)
}

func CreatePrescriptionOrder(c *gin.Context) {
	prescriptionID := c.Param("prescriptionID")

	var req models.CreatePrescriptionOrderRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	presID, _ := strconv.ParseUint(prescriptionID, 10, 32)

	order := models.PrescriptionOrder{
		PrescriptionID: uint(presID),
		PharmacyID:     req.PharmacyID,
		Quantity:       req.Quantity,
		Status:         "pending",
		OrderDate:      time.Now(),
	}

	if err := config.GetDB().Create(&order).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create order"})
		return
	}

	c.JSON(http.StatusCreated, order)
}

func GetPrescriptionOrders(c *gin.Context) {
	prescriptionID := c.Param("prescriptionID")
	page := c.DefaultQuery("page", "1")

	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var orders []models.PrescriptionOrder
	var total int64

	config.GetDB().Where("prescription_id = ?", prescriptionID).Model(&models.PrescriptionOrder{}).Count(&total)
	config.GetDB().Where("prescription_id = ?", prescriptionID).Offset(offset).Limit(limit).Find(&orders)

	c.JSON(http.StatusOK, gin.H{
		"data":      orders,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}
