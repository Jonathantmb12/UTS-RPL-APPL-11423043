package models

import (
	"time"

	"gorm.io/gorm"
)

// Patient model (dari User di Laravel tapi khusus patient data)
type Patient struct {
	ID               uint           `gorm:"primaryKey" json:"id"`
	UserID           uint           `gorm:"index" json:"user_id"` // Reference ke User di Auth Service
	Name             string         `json:"name"`
	Email            string         `gorm:"unique;index" json:"email"`
	PhoneNumber      string         `json:"phone_number"`
	DateOfBirth      *time.Time     `json:"date_of_birth,omitempty"`
	Gender           string         `json:"gender"` // male, female, other
	Address          string         `json:"address"`
	EmergencyContact string         `json:"emergency_contact"`
	BloodType        string         `json:"blood_type"`
	Allergies        string         `json:"allergies"`
	CreatedAt        time.Time      `json:"created_at"`
	UpdatedAt        time.Time      `json:"updated_at"`
	DeletedAt        gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	// Relationships
	EHRs            []ElectronicHealthRecord `gorm:"foreignKey:PatientID" json:"ehrs,omitempty"`
	LabResults      []LabResult              `gorm:"foreignKey:PatientID" json:"lab_results,omitempty"`
	PatientOutcomes []PatientOutcome         `gorm:"foreignKey:PatientID" json:"patient_outcomes,omitempty"`
}

// ElectronicHealthRecord model
type ElectronicHealthRecord struct {
	ID                 uint           `gorm:"primaryKey" json:"id"`
	PatientID          uint           `gorm:"index" json:"patient_id"`
	RecordDate         time.Time      `json:"record_date"`
	MedicalHistory     string         `json:"medical_history"`
	CurrentMedications string         `json:"current_medications"`
	SurgicalHistory    string         `json:"surgical_history"`
	FamilyHistory      string         `json:"family_history"`
	Vaccinations       string         `json:"vaccinations"`
	CreatedAt          time.Time      `json:"created_at"`
	UpdatedAt          time.Time      `json:"updated_at"`
	DeletedAt          gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	Patient Patient `gorm:"foreignKey:PatientID" json:"patient,omitempty"`
}

// LabResult model
type LabResult struct {
	ID           uint           `gorm:"primaryKey" json:"id"`
	PatientID    uint           `gorm:"index" json:"patient_id"`
	TestName     string         `json:"test_name"`
	TestType     string         `json:"test_type"`
	LabName      string         `json:"lab_name"`
	TestDate     time.Time      `json:"test_date"`
	ResultDate   *time.Time     `json:"result_date,omitempty"`
	NormalRange  string         `json:"normal_range"`
	Result       string         `json:"result"`
	ResultStatus string         `json:"result_status"` // normal, abnormal, critical
	Unit         string         `json:"unit"`
	Notes        string         `json:"notes"`
	Status       string         `json:"status"` // pending, completed, cancelled
	CreatedAt    time.Time      `json:"created_at"`
	UpdatedAt    time.Time      `json:"updated_at"`
	DeletedAt    gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	Patient Patient `gorm:"foreignKey:PatientID" json:"patient,omitempty"`
}

// PatientOutcome model
type PatientOutcome struct {
	ID               uint           `gorm:"primaryKey" json:"id"`
	PatientID        uint           `gorm:"index" json:"patient_id"`
	OutcomeDate      time.Time      `json:"outcome_date"`
	OutcomeType      string         `json:"outcome_type"` // recovery, worsening, stable, etc
	Description      string         `json:"description"`
	MeasurementType  string         `json:"measurement_type"` // vital signs, lab results, etc
	MeasurementValue string         `json:"measurement_value"`
	Status           string         `json:"status"`
	Notes            string         `json:"notes"`
	CreatedAt        time.Time      `json:"created_at"`
	UpdatedAt        time.Time      `json:"updated_at"`
	DeletedAt        gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	Patient Patient `gorm:"foreignKey:PatientID" json:"patient,omitempty"`
}

// Request/Response models
type CreatePatientRequest struct {
	UserID           uint    `json:"user_id" binding:"required"`
	Name             string  `json:"name" binding:"required"`
	Email            string  `json:"email" binding:"required,email"`
	PhoneNumber      string  `json:"phone_number" binding:"required"`
	DateOfBirth      *string `json:"date_of_birth,omitempty"`
	Gender           string  `json:"gender" binding:"required"`
	Address          string  `json:"address" binding:"required"`
	EmergencyContact string  `json:"emergency_contact" binding:"required"`
	BloodType        string  `json:"blood_type" binding:"required"`
	Allergies        string  `json:"allergies"`
}

type CreateLabResultRequest struct {
	TestName     string `json:"test_name" binding:"required"`
	TestType     string `json:"test_type" binding:"required"`
	LabName      string `json:"lab_name" binding:"required"`
	TestDate     string `json:"test_date" binding:"required"`
	NormalRange  string `json:"normal_range"`
	Result       string `json:"result"`
	ResultStatus string `json:"result_status"`
	Unit         string `json:"unit"`
	Notes        string `json:"notes"`
}

type CreateEHRRequest struct {
	MedicalHistory     string `json:"medical_history"`
	CurrentMedications string `json:"current_medications"`
	SurgicalHistory    string `json:"surgical_history"`
	FamilyHistory      string `json:"family_history"`
	Vaccinations       string `json:"vaccinations"`
}
