<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\PharmacyInventory;
use App\Models\ElectronicHealthRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n🌱 Seeding MediTrack Database...\n";

        // ============ ADMIN USERS ============
        echo "📋 Creating Admin Users...\n";
        
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@meditrack.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone_number' => '08123456789',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // ============ DOCTOR USERS ============
        echo "👨‍⚕️ Creating Doctor Users...\n";

        $doctors = [];
        $doctorData = [
            [
                'name' => 'Dr. John Smith',
                'email' => 'dr.john@meditrack.local',
                'specialization' => 'Cardiology',
                'license_number' => 'DOC-2025-001',
                'hospital_name' => 'Central Hospital',
            ],
            [
                'name' => 'Dr. Sarah Johnson',
                'email' => 'dr.sarah@meditrack.local',
                'specialization' => 'Pediatrics',
                'license_number' => 'DOC-2025-002',
                'hospital_name' => 'Central Hospital',
            ],
            [
                'name' => 'Dr. Michael Brown',
                'email' => 'dr.michael@meditrack.local',
                'specialization' => 'Orthopedics',
                'license_number' => 'DOC-2025-003',
                'hospital_name' => 'Medical Center',
            ],
            [
                'name' => 'Dr. Emily Davis',
                'email' => 'dr.emily@meditrack.local',
                'specialization' => 'Neurology',
                'license_number' => 'DOC-2025-004',
                'hospital_name' => 'City Hospital',
            ],
        ];

        foreach ($doctorData as $doctorInfo) {
            $doctor = User::create([
                'name' => $doctorInfo['name'],
                'email' => $doctorInfo['email'],
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'specialization' => $doctorInfo['specialization'],
                'license_number' => $doctorInfo['license_number'],
                'hospital_name' => $doctorInfo['hospital_name'],
                'phone_number' => '08' . rand(100000000, 999999999),
                'is_verified' => true,
                'is_active' => true,
            ]);
            $doctors[] = $doctor;
            echo "  ✓ {$doctor->name} ({$doctor->specialization})\n";
        }

        // ============ PHARMACIST USERS ============
        echo "💊 Creating Pharmacist Users...\n";

        $pharmacists = [];
        $pharmacistData = [
            [
                'name' => 'Pharmacist Alex',
                'email' => 'pharmacist@meditrack.local',
                'pharmacy_name' => 'Central Pharmacy',
                'pharmacy_license' => 'PHARM-2025-001',
                'pharmacy_address' => '123 Main Street, City Center',
            ],
            [
                'name' => 'Pharmacist Emma',
                'email' => 'emma@meditrack.local',
                'pharmacy_name' => 'Downtown Pharmacy',
                'pharmacy_license' => 'PHARM-2025-002',
                'pharmacy_address' => '456 Oak Avenue, Downtown',
            ],
            [
                'name' => 'Pharmacist David',
                'email' => 'david@meditrack.local',
                'pharmacy_name' => 'Express Pharmacy',
                'pharmacy_license' => 'PHARM-2025-003',
                'pharmacy_address' => '789 Elm Street, Suburbs',
            ],
        ];

        foreach ($pharmacistData as $pharmacistInfo) {
            $pharmacist = User::create([
                'name' => $pharmacistInfo['name'],
                'email' => $pharmacistInfo['email'],
                'password' => Hash::make('password123'),
                'role' => 'pharmacist',
                'pharmacy_name' => $pharmacistInfo['pharmacy_name'],
                'pharmacy_license' => $pharmacistInfo['pharmacy_license'],
                'pharmacy_address' => $pharmacistInfo['pharmacy_address'],
                'phone_number' => '08' . rand(100000000, 999999999),
                'is_verified' => true,
                'is_active' => true,
            ]);
            $pharmacists[] = $pharmacist;
            echo "  ✓ {$pharmacist->name} ({$pharmacist->pharmacy_name})\n";
        }

        // ============ PATIENT USERS ============
        echo "🩺 Creating Patient Users...\n";

        $patients = [];
        $bloodTypes = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
        
        $patientData = [
            ['name' => 'John Doe', 'email' => 'patient@meditrack.local'],
            ['name' => 'Jane Smith', 'email' => 'jane@meditrack.local'],
            ['name' => 'Bob Wilson', 'email' => 'bob@meditrack.local'],
            ['name' => 'Alice Green', 'email' => 'alice@meditrack.local'],
            ['name' => 'Charlie Taylor', 'email' => 'charlie@meditrack.local'],
            ['name' => 'Diana Anderson', 'email' => 'diana@meditrack.local'],
            ['name' => 'Edward Thomas', 'email' => 'edward@meditrack.local'],
            ['name' => 'Fiona Martinez', 'email' => 'fiona@meditrack.local'],
        ];

        foreach ($patientData as $patientInfo) {
            $patient = User::create([
                'name' => $patientInfo['name'],
                'email' => $patientInfo['email'],
                'password' => Hash::make('password123'),
                'role' => 'patient',
                'date_of_birth' => now()->subYears(rand(18, 70))->format('Y-m-d'),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'phone_number' => '08' . rand(100000000, 999999999),
                'address' => rand(1, 999) . ' ' . ['Main', 'Oak', 'Elm'][rand(0, 2)] . ' Street',
                'blood_type' => $bloodTypes[rand(0, 7)],
                'allergies' => rand(0, 1) ? 'Penicillin' : null,
                'emergency_contact' => '08' . rand(100000000, 999999999),
                'is_verified' => true,
                'is_active' => true,
            ]);
            $patients[] = $patient;
            echo "  ✓ {$patient->name} (Blood: {$patient->blood_type})\n";
        }

        // ============ CREATE APPOINTMENTS ============
        echo "\n📅 Creating Appointments...\n";

        for ($i = 0; $i < 10; $i++) {
            $appointment = Appointment::create([
                'patient_id' => $patients[array_rand($patients)]->id,
                'doctor_id' => $doctors[array_rand($doctors)]->id,
                'appointment_date' => now()->addDays(rand(1, 30))->setHour(rand(9, 17))->setMinute(0),
                'duration_minutes' => 30,
                'status' => ['scheduled', 'confirmed', 'completed'][rand(0, 2)],
                'reason_for_visit' => ['Regular checkup', 'Follow-up', 'Consultation'][rand(0, 2)],
                'consultation_type' => 'in-person',
            ]);
            echo "  ✓ Appointment #{$appointment->id}\n";
        }

        // ============ CREATE ELECTRONIC HEALTH RECORDS ============
        echo "\n📋 Creating Electronic Health Records...\n";

        foreach ($patients as $patient) {
            $doctor = $doctors[array_rand($doctors)];
            ElectronicHealthRecord::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'medical_history' => 'Previous hospital visits: 2 times',
                'current_medications' => 'Aspirin 100mg, Vitamin D',
                'allergies' => $patient->allergies,
                'previous_surgeries' => rand(0, 1) ? 'Appendectomy' : null,
                'family_history' => ['Diabetes', 'Heart disease', 'Hypertension'][rand(0, 2)],
                'lifestyle_notes' => 'Exercise regularly',
                'blood_type' => $patient->blood_type,
                'height_cm' => rand(150, 190),
                'weight_kg' => rand(50, 100),
                'blood_pressure_systolic' => rand(100, 140),
                'blood_pressure_diastolic' => rand(60, 90),
                'heart_rate' => rand(60, 100),
                'body_temperature_celsius' => rand(36, 37),
            ]);
            echo "  ✓ EHR for {$patient->name}\n";
        }

        // ============ CREATE PRESCRIPTIONS ============
        echo "\n💊 Creating Prescriptions...\n";

        $medications = [
            ['name' => 'Paracetamol', 'dosage' => '500mg'],
            ['name' => 'Ibuprofen', 'dosage' => '200mg'],
            ['name' => 'Amoxicillin', 'dosage' => '500mg'],
            ['name' => 'Aspirin', 'dosage' => '100mg'],
            ['name' => 'Metformin', 'dosage' => '500mg'],
            ['name' => 'Lisinopril', 'dosage' => '10mg'],
        ];

        for ($i = 0; $i < 15; $i++) {
            $medication = $medications[rand(0, count($medications) - 1)];
            $prescription = Prescription::create([
                'patient_id' => $patients[array_rand($patients)]->id,
                'doctor_id' => $doctors[array_rand($doctors)]->id,
                'medication_name' => $medication['name'],
                'dosage' => $medication['dosage'],
                'frequency' => ['Once daily', 'Twice daily', 'Three times daily'][rand(0, 2)],
                'quantity' => rand(10, 30),
                'duration_days' => rand(7, 30),
                'prescribed_date' => now()->subDays(rand(0, 10)),
                'expiration_date' => now()->addDays(rand(1, 30)),
                'status' => 'active',
                'instructions' => 'Take with food',
            ]);
            echo "  ✓ Prescription for {$medication['name']} #{$prescription->id}\n";
        }

        // ============ CREATE PHARMACY INVENTORY ============
        echo "\n🏥 Creating Pharmacy Inventory...\n";

        $medicationsList = [
            ['name' => 'Paracetamol 500mg', 'generic' => 'Paracetamol', 'price' => 5000],
            ['name' => 'Ibuprofen 200mg', 'generic' => 'Ibuprofen', 'price' => 7500],
            ['name' => 'Amoxicillin 500mg', 'generic' => 'Amoxicillin', 'price' => 12000],
            ['name' => 'Aspirin 100mg', 'generic' => 'Aspirin', 'price' => 4000],
            ['name' => 'Metformin 500mg', 'generic' => 'Metformin', 'price' => 8000],
            ['name' => 'Lisinopril 10mg', 'generic' => 'Lisinopril', 'price' => 15000],
            ['name' => 'Atorvastatin 20mg', 'generic' => 'Atorvastatin', 'price' => 18000],
            ['name' => 'Omeprazole 20mg', 'generic' => 'Omeprazole', 'price' => 6000],
        ];

        foreach ($pharmacists as $pharmacy) {
            foreach ($medicationsList as $med) {
                PharmacyInventory::create([
                    'pharmacy_id' => $pharmacy->id,
                    'medication_name' => $med['name'],
                    'generic_name' => $med['generic'],
                    'sku' => strtoupper(str_replace(' ', '-', $med['generic'])) . '-' . $pharmacy->id,
                    'stock_quantity' => rand(50, 200),
                    'reorder_level' => 20,
                    'reorder_quantity' => 50,
                    'unit_price' => $med['price'],
                    'manufacturer' => ['Pharma Co', 'Med Lab', 'Health Plus'][rand(0, 2)],
                    'batch_number' => 'BATCH-' . rand(1000, 9999),
                    'expiration_date' => now()->addMonths(rand(6, 24))->format('Y-m-d'),
                    'is_active' => true,
                ]);
            }
            echo "  ✓ Inventory for {$pharmacy->pharmacy_name}\n";
        }

        echo "\n✅ Database seeding completed successfully!\n";
        echo "\n📊 Summary:\n";
        echo "  • 1 Admin\n";
        echo "  • 4 Doctors\n";
        echo "  • 3 Pharmacists\n";
        echo "  • 8 Patients\n";
        echo "  • 10 Appointments\n";
        echo "  • 8 Electronic Health Records\n";
        echo "  • 15 Prescriptions\n";
        echo "  • 24 Pharmacy Inventory Items\n";
        echo "\n🔐 Default Password (all users): password123\n\n";
    }
}
