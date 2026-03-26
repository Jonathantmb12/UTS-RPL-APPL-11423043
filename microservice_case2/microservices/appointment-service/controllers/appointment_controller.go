package controllers

import (
	"fmt"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"appointment-service/config"
	"appointment-service/models"
)

func CreateAppointment(c *gin.Context) {
	var req models.CreateAppointmentRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	aptDate, err := time.Parse("2006-01-02 15:04:05", req.AppointmentDate)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid date format"})
		return
	}

	appointment := models.Appointment{
		PatientID:        req.PatientID,
		DoctorID:         req.DoctorID,
		AppointmentDate:  aptDate,
		DurationMinutes:  30,
		Status:           "scheduled",
		ReasonForVisit:   req.ReasonForVisit,
		ConsultationType: req.ConsultationType,
		MeetingLink:      req.MeetingLink,
	}

	if err := config.GetDB().Create(&appointment).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create appointment"})
		return
	}

	c.JSON(http.StatusCreated, appointment)
}

func GetAppointment(c *gin.Context) {
	id := c.Param("id")

	var appointment models.Appointment
	if err := config.GetDB().First(&appointment, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Appointment not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, appointment)
}

func GetAppointments(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	patientID := c.Query("patient_id")
	doctorID := c.Query("doctor_id")
	status := c.Query("status")

	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var appointments []models.Appointment
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

	query.Model(&models.Appointment{}).Count(&total)
	query.Offset(offset).Limit(limit).Find(&appointments)

	c.JSON(http.StatusOK, gin.H{
		"data":      appointments,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

func UpdateAppointment(c *gin.Context) {
	id := c.Param("id")

	var appointment models.Appointment
	if err := config.GetDB().First(&appointment, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Appointment not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	var req models.UpdateAppointmentRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	if err := config.GetDB().Model(&appointment).Updates(req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update appointment"})
		return
	}

	c.JSON(http.StatusOK, appointment)
}

func CancelAppointment(c *gin.Context) {
	id := c.Param("id")

	var appointment models.Appointment
	if err := config.GetDB().First(&appointment, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Appointment not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	var req models.CancelAppointmentRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	now := time.Now()
	if err := config.GetDB().Model(&appointment).Updates(map[string]interface{}{
		"status":              "cancelled",
		"cancelled_at":        now,
		"cancellation_reason": req.CancellationReason,
	}).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to cancel appointment"})
		return
	}

	c.JSON(http.StatusOK, appointment)
}

func GetAvailableSlots(c *gin.Context) {
	doctorID := c.Query("doctor_id")
	date := c.Query("date")

	if doctorID == "" || date == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "doctor_id and date are required"})
		return
	}

	startDate, err := time.Parse("2006-01-02", date)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid date format"})
		return
	}

	endDate := startDate.AddDate(0, 0, 1)

	var appointments []models.Appointment
	config.GetDB().Where("doctor_id = ? AND appointment_date >= ? AND appointment_date < ? AND status != ?",
		doctorID, startDate, endDate, "cancelled").Find(&appointments)

	// Generate available slots (9 AM to 5 PM, 30 min intervals)
	bookedSlots := make(map[string]bool)
	for _, apt := range appointments {
		bookedSlots[apt.AppointmentDate.Format("15:04")] = true
	}

	availableSlots := []string{}
	for hour := 9; hour < 17; hour++ {
		for min := 0; min < 60; min += 30 {
			slotTime := fmt.Sprintf("%02d:%02d", hour, min)
			if !bookedSlots[slotTime] {
				availableSlots = append(availableSlots, slotTime)
			}
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"date":  date,
		"slots": availableSlots,
	})
}
