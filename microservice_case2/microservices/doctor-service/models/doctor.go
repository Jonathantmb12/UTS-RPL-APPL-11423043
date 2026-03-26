package models

import (
	"time"

	"gorm.io/gorm"
)

// Doctor model
type Doctor struct {
	ID             uint           `gorm:"primaryKey" json:"id"`
	UserID         uint           `gorm:"index" json:"user_id"`
	Name           string         `json:"name"`
	Email          string         `gorm:"unique;index" json:"email"`
	Specialization string         `json:"specialization"`
	LicenseNumber  string         `gorm:"unique;index" json:"license_number"`
	HospitalName   string         `json:"hospital_name"`
	PhoneNumber    *string        `json:"phone_number,omitempty"`
	Address        *string        `json:"address,omitempty"`
	ProfilePicture *string        `json:"profile_picture,omitempty"`
	IsActive       bool           `json:"is_active"`
	CreatedAt      time.Time      `json:"created_at"`
	UpdatedAt      time.Time      `json:"updated_at"`
	DeletedAt      gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	// Relationships
	PerformanceMetrics []DoctorPerformanceMetric `gorm:"foreignKey:DoctorID" json:"performance_metrics,omitempty"`
}

// DoctorPerformanceMetric model
type DoctorPerformanceMetric struct {
	ID                     uint           `gorm:"primaryKey" json:"id"`
	DoctorID               uint           `gorm:"index" json:"doctor_id"`
	TotalConsultations     int            `json:"total_consultations"`
	AvgPatientSatisfaction float64        `json:"avg_patient_satisfaction"`
	TotalPrescriptions     int            `json:"total_prescriptions"`
	PatientRetentionRate   float64        `json:"patient_retention_rate"`
	AverageRefusalRate     float64        `json:"average_refusal_rate"`
	PendingApprovals       int            `json:"pending_approvals"`
	CompletedApprovals     int            `json:"completed_approvals"`
	RejectedApprovals      int            `json:"rejected_approvals"`
	CreatedAt              time.Time      `json:"created_at"`
	UpdatedAt              time.Time      `json:"updated_at"`
	DeletedAt              gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`

	Doctor Doctor `gorm:"foreignKey:DoctorID" json:"doctor,omitempty"`
}

// Request/Response models
type CreateDoctorRequest struct {
	UserID         uint    `json:"user_id" binding:"required"`
	Name           string  `json:"name" binding:"required"`
	Email          string  `json:"email" binding:"required,email"`
	Specialization string  `json:"specialization" binding:"required"`
	LicenseNumber  string  `json:"license_number" binding:"required"`
	HospitalName   string  `json:"hospital_name" binding:"required"`
	PhoneNumber    *string `json:"phone_number,omitempty"`
	Address        *string `json:"address,omitempty"`
}

type UpdateDoctorRequest struct {
	Name           string  `json:"name"`
	Email          string  `json:"email"`
	Specialization string  `json:"specialization"`
	HospitalName   string  `json:"hospital_name"`
	PhoneNumber    *string `json:"phone_number,omitempty"`
	Address        *string `json:"address,omitempty"`
	IsActive       *bool   `json:"is_active,omitempty"`
}

type DoctorListResponse struct {
	ID             uint                     `json:"id"`
	UserID         uint                     `json:"user_id"`
	Name           string                   `json:"name"`
	Email          string                   `json:"email"`
	Specialization string                   `json:"specialization"`
	LicenseNumber  string                   `json:"license_number"`
	HospitalName   string                   `json:"hospital_name"`
	IsActive       bool                     `json:"is_active"`
	Metrics        *DoctorPerformanceMetric `json:"metrics,omitempty"`
}
