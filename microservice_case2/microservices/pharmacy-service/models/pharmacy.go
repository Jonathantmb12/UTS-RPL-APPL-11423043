package models

import (
	"time"

	"gorm.io/gorm"
)

type PharmacyInventory struct {
	ID              uint           `gorm:"primaryKey" json:"id"`
	PharmacyID      uint           `gorm:"index" json:"pharmacy_id"`
	MedicationName  string         `gorm:"index" json:"medication_name"`
	GenericName     string         `json:"generic_name"`
	SKU             string         `gorm:"unique;index" json:"sku"`
	StockQuantity   int            `json:"stock_quantity"`
	ReorderLevel    int            `json:"reorder_level"`
	ReorderQuantity int            `json:"reorder_quantity"`
	UnitPrice       float64        `json:"unit_price"`
	BatchNumber     string         `json:"batch_number"`
	ExpirationDate  time.Time      `json:"expiration_date"`
	Manufacturer    string         `json:"manufacturer"`
	Description     string         `json:"description"`
	IsActive        bool           `json:"is_active"`
	CreatedAt       time.Time      `json:"created_at"`
	UpdatedAt       time.Time      `json:"updated_at"`
	DeletedAt       gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`
}

type Payment struct {
	ID             uint           `gorm:"primaryKey" json:"id"`
	PharmacyID     uint           `gorm:"index" json:"pharmacy_id"`
	PatientID      uint           `gorm:"index" json:"patient_id"`
	PrescriptionID uint           `json:"prescription_id"`
	Amount         float64        `json:"amount"`
	PaymentMethod  string         `json:"payment_method"` // cash, card, insurance
	Status         string         `json:"status"`         // pending, completed, failed
	PaymentDate    *time.Time     `json:"payment_date,omitempty"`
	ReceiptNumber  *string        `json:"receipt_number,omitempty"`
	Notes          string         `json:"notes"`
	CreatedAt      time.Time      `json:"created_at"`
	UpdatedAt      time.Time      `json:"updated_at"`
	DeletedAt      gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`
}

type CreateInventoryRequest struct {
	PharmacyID      uint    `json:"pharmacy_id" binding:"required"`
	MedicationName  string  `json:"medication_name" binding:"required"`
	GenericName     string  `json:"generic_name"`
	SKU             string  `json:"sku" binding:"required"`
	StockQuantity   int     `json:"stock_quantity" binding:"required"`
	ReorderLevel    int     `json:"reorder_level"`
	ReorderQuantity int     `json:"reorder_quantity"`
	UnitPrice       float64 `json:"unit_price" binding:"required"`
	BatchNumber     string  `json:"batch_number"`
	ExpirationDate  string  `json:"expiration_date" binding:"required"`
	Manufacturer    string  `json:"manufacturer"`
	Description     string  `json:"description"`
}

type CreatePaymentRequest struct {
	PatientID      uint    `json:"patient_id" binding:"required"`
	PrescriptionID uint    `json:"prescription_id" binding:"required"`
	Amount         float64 `json:"amount" binding:"required"`
	PaymentMethod  string  `json:"payment_method" binding:"required"`
	Notes          string  `json:"notes"`
}
