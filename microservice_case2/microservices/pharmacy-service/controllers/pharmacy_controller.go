package controllers

import (
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"pharmacy-service/config"
	"pharmacy-service/models"
)

func CreateInventory(c *gin.Context) {
	var req models.CreateInventoryRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	expDate, _ := time.Parse("2006-01-02", req.ExpirationDate)

	inventory := models.PharmacyInventory{
		PharmacyID:      req.PharmacyID,
		MedicationName:  req.MedicationName,
		GenericName:     req.GenericName,
		SKU:             req.SKU,
		StockQuantity:   req.StockQuantity,
		ReorderLevel:    req.ReorderLevel,
		ReorderQuantity: req.ReorderQuantity,
		UnitPrice:       req.UnitPrice,
		BatchNumber:     req.BatchNumber,
		ExpirationDate:  expDate,
		Manufacturer:    req.Manufacturer,
		Description:     req.Description,
		IsActive:        true,
	}

	if err := config.GetDB().Create(&inventory).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create inventory"})
		return
	}

	c.JSON(http.StatusCreated, inventory)
}

func GetInventory(c *gin.Context) {
	id := c.Param("id")

	var inventory models.PharmacyInventory
	if err := config.GetDB().First(&inventory, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Inventory not found"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, inventory)
}

func GetInventories(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	pharmacyID := c.Query("pharmacy_id")

	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var inventories []models.PharmacyInventory
	var total int64

	query := config.GetDB()
	if pharmacyID != "" {
		query = query.Where("pharmacy_id = ?", pharmacyID)
	}

	query.Model(&models.PharmacyInventory{}).Count(&total)
	query.Offset(offset).Limit(limit).Find(&inventories)

	c.JSON(http.StatusOK, gin.H{
		"data":      inventories,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

func UpdateInventory(c *gin.Context) {
	id := c.Param("id")

	var inventory models.PharmacyInventory
	if err := config.GetDB().First(&inventory, id).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.JSON(http.StatusNotFound, gin.H{"error": "Inventory not found"})
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

	if err := config.GetDB().Model(&inventory).Updates(updateData).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update inventory"})
		return
	}

	c.JSON(http.StatusOK, inventory)
}

func GetLowStockItems(c *gin.Context) {
	var inventories []models.PharmacyInventory
	config.GetDB().Where("stock_quantity <= reorder_level").Find(&inventories)

	c.JSON(http.StatusOK, inventories)
}

func CreatePayment(c *gin.Context) {
	var req models.CreatePaymentRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	now := time.Now()
	payment := models.Payment{
		PharmacyID:     1, // Assuming pharmacy ID from context or header
		PatientID:      req.PatientID,
		PrescriptionID: req.PrescriptionID,
		Amount:         req.Amount,
		PaymentMethod:  req.PaymentMethod,
		Status:         "completed",
		PaymentDate:    &now,
		ReceiptNumber:  genReceiptNumber(),
		Notes:          req.Notes,
	}

	if err := config.GetDB().Create(&payment).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create payment"})
		return
	}

	c.JSON(http.StatusCreated, payment)
}

func GetPayments(c *gin.Context) {
	page := c.DefaultQuery("page", "1")
	patientID := c.Query("patient_id")

	pageNum, _ := strconv.Atoi(page)
	limit := 10
	offset := (pageNum - 1) * limit

	var payments []models.Payment
	var total int64

	query := config.GetDB()
	if patientID != "" {
		query = query.Where("patient_id = ?", patientID)
	}

	query.Model(&models.Payment{}).Count(&total)
	query.Offset(offset).Limit(limit).Find(&payments)

	c.JSON(http.StatusOK, gin.H{
		"data":      payments,
		"total":     total,
		"page":      pageNum,
		"page_size": limit,
	})
}

func genReceiptNumber() *string {
	receipt := "RCP" + strconv.FormatInt(time.Now().UnixNano(), 10)
	return &receipt
}
