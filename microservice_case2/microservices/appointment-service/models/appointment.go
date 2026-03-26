package models

import (
	"time"

	"gorm.io/gorm"
)

type Appointment struct {
	ID                 uint           `gorm:"primaryKey" json:"id"`
	PatientID          uint           `gorm:"index" json:"patient_id"`
	DoctorID           uint           `gorm:"index" json:"doctor_id"`
	AppointmentDate    time.Time      `gorm:"index" json:"appointment_date"`
	DurationMinutes    int            `json:"duration_minutes"`
	Status             string         `json:"status"` // scheduled, confirmed, completed, cancelled
	ReasonForVisit     string         `json:"reason_for_visit"`
	Notes              string         `json:"notes"`
	ConsultationType   string         `json:"consultation_type"` // in-person, video, phone
	MeetingLink        *string        `json:"meeting_link,omitempty"`
	CancelledAt        *time.Time     `json:"cancelled_at,omitempty"`
	CancellationReason *string        `json:"cancellation_reason,omitempty"`
	CreatedAt          time.Time      `json:"created_at"`
	UpdatedAt          time.Time      `json:"updated_at"`
	DeletedAt          gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`
}

type CreateAppointmentRequest struct {
	PatientID        uint    `json:"patient_id" binding:"required"`
	DoctorID         uint    `json:"doctor_id" binding:"required"`
	AppointmentDate  string  `json:"appointment_date" binding:"required"`
	ReasonForVisit   string  `json:"reason_for_visit" binding:"required"`
	ConsultationType string  `json:"consultation_type"`
	MeetingLink      *string `json:"meeting_link,omitempty"`
}

type UpdateAppointmentRequest struct {
	Status           *string `json:"status,omitempty"`
	ReasonForVisit   *string `json:"reason_for_visit,omitempty"`
	Notes            *string `json:"notes,omitempty"`
	ConsultationType *string `json:"consultation_type,omitempty"`
	MeetingLink      *string `json:"meeting_link,omitempty"`
}

type CancelAppointmentRequest struct {
	CancellationReason string `json:"cancellation_reason" binding:"required"`
}
