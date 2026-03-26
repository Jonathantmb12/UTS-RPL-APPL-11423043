package models

import (
	"time"

	"gorm.io/gorm"
)

type User struct {
	ID                 uint      `gorm:"primaryKey" json:"id"`
	Name               string    `json:"name"`
	Email              string    `gorm:"unique;index" json:"email"`
	Password           string    `json:"-"`
	Role               string    `json:"role"` // admin, doctor, patient, pharmacist
	CreatedAt          time.Time `json:"created_at"`
	UpdatedAt          time.Time `json:"updated_at"`
	DeletedAt          gorm.DeletedAt `gorm:"index" json:"deleted_at,omitempty"`
	
	// Doctor specific fields
	Specialization     *string `json:"specialization,omitempty"`
	LicenseNumber      *string `gorm:"unique;index" json:"license_number,omitempty"`
	HospitalName       *string `json:"hospital_name,omitempty"`
	
	// Patient specific fields
	DateOfBirth        *time.Time `json:"date_of_birth,omitempty"`
	Gender             *string    `json:"gender,omitempty"` // male, female, other
	PhoneNumber        *string    `json:"phone_number,omitempty"`
	Address            *string    `json:"address,omitempty"`
	EmergencyContact   *string    `json:"emergency_contact,omitempty"`
	BloodType          *string    `json:"blood_type,omitempty"`
	Allergies          *string    `json:"allergies,omitempty"`
	
	// Pharmacist specific fields
	PharmacyName       *string `json:"pharmacy_name,omitempty"`
	PharmacyLicense    *string `gorm:"unique;index" json:"pharmacy_license,omitempty"`
	PharmacyAddress    *string `json:"pharmacy_address,omitempty"`
	
	// Common fields
	ProfilePicture     *string    `json:"profile_picture,omitempty"`
	IsVerified         bool       `json:"is_verified"`
	VerificationToken  *string    `json:"verification_token,omitempty"`
	VerifiedAt         *time.Time `json:"verified_at,omitempty"`
	LastLoginAt        *time.Time `json:"last_login_at,omitempty"`
	IsActive           bool       `json:"is_active"`
}

type LoginRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required"`
}

type LoginResponse struct {
	ID    uint   `json:"id"`
	Email string `json:"email"`
	Name  string `json:"name"`
	Role  string `json:"role"`
	Token string `json:"token"`
}

type RegisterRequest struct {
	Name            string `json:"name" binding:"required"`
	Email           string `json:"email" binding:"required,email"`
	Password        string `json:"password" binding:"required,min=6"`
	Role            string `json:"role" binding:"required"`
	PhoneNumber     *string `json:"phone_number,omitempty"`
	DateOfBirth     *string `json:"date_of_birth,omitempty"`
	Gender          *string `json:"gender,omitempty"`
	Address         *string `json:"address,omitempty"`
	BloodType       *string `json:"blood_type,omitempty"`
	Allergies       *string `json:"allergies,omitempty"`
	Specialization  *string `json:"specialization,omitempty"`
	LicenseNumber   *string `json:"license_number,omitempty"`
	HospitalName    *string `json:"hospital_name,omitempty"`
	PharmacyName    *string `json:"pharmacy_name,omitempty"`
	PharmacyLicense *string `json:"pharmacy_license,omitempty"`
	PharmacyAddress *string `json:"pharmacy_address,omitempty"`
}

type UpdateProfileRequest struct {
	Name           string  `json:"name"`
	PhoneNumber    *string `json:"phone_number,omitempty"`
	Address        *string `json:"address,omitempty"`
	ProfilePicture *string `json:"profile_picture,omitempty"`
}
