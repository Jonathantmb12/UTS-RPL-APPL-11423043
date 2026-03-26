package models

import (
	"time"

	"gorm.io/gorm"
)

type Prescription struct {
	ID                 uint           `gorm:"primaryKey" json:"id"`
	PatientID          uint           `gorm:"index" json:"patient_id"`
	DoctorID           uint           `gorm:"index" json:"doctor_id"`
	AppointmentID      *uint          `json:"appointment_id,omitempty"`
	MedicationName     string         `json:"medication_name"`
	Description        string         `json:"description"`
	Dosage             string         `json:"dosage"`
	Frequency          string         `json:"frequency"`
	Quantity           int            `json:"quantity"`
	DurationDays       int            `json:"duration_days"`
	Instructions       string         `json:"instructions"`
	SideEffectsWarning string         `json:"side_effects_warning"`
	Status             string         `json:"status"` // active, completed, cancelled
	PrescribedDate     time.Time      `json:"prescribed_date"`
	ExpirationDate     time.Time      `json:"expiration_date"`
	CreatedAt          time.Time      `json:"created_at"`
	UpdatedAt          time.Time      `json:"updated_at"`
	DeletedAt          gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	Orders []PrescriptionOrder `gorm:"foreignKey:PrescriptionID" json:"orders,omitempty"`
}

type PrescriptionOrder struct {
	ID             uint           `gorm:"primaryKey" json:"id"`
	PrescriptionID uint           `gorm:"index" json:"prescription_id"`
	PharmacyID     uint           `gorm:"index" json:"pharmacy_id"`
	Quantity       int            `json:"quantity"`
	Status         string         `json:"status"` // pending, dispensed, completed
	OrderDate      time.Time      `json:"order_date"`
	DispensedDate  *time.Time     `json:"dispensed_date,omitempty"`
	CreatedAt      time.Time      `json:"created_at"`
	UpdatedAt      time.Time      `json:"updated_at"`
	DeletedAt      gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	Prescription Prescription `gorm:"foreignKey:PrescriptionID" json:"prescription,omitempty"`
}

type CreatePrescriptionRequest struct {
	PatientID          uint   `json:"patient_id" binding:"required"`
	DoctorID           uint   `json:"doctor_id" binding:"required"`
	AppointmentID      *uint  `json:"appointment_id,omitempty"`
	MedicationName     string `json:"medication_name" binding:"required"`
	Description        string `json:"description"`
	Dosage             string `json:"dosage" binding:"required"`
	Frequency          string `json:"frequency" binding:"required"`
	Quantity           int    `json:"quantity" binding:"required"`
	DurationDays       int    `json:"duration_days" binding:"required"`
	Instructions       string `json:"instructions"`
	SideEffectsWarning string `json:"side_effects_warning"`
}

type CreatePrescriptionOrderRequest struct {
	PharmacyID uint `json:"pharmacy_id" binding:"required"`
	Quantity   int  `json:"quantity" binding:"required"`
}
