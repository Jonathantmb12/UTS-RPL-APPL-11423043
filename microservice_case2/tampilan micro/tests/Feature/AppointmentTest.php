<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $patient;
    protected User $doctor;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $this->patient = User::factory()->create(['role' => 'patient']);
    }

    public function test_patient_can_book_appointment()
    {
        $response = $this->actingAs($this->patient)
            ->postJson('/api/appointments', [
                'doctor_id' => $this->doctor->id,
                'appointment_date' => now()->addDay()->format('Y-m-d H:i:s'),
                'reason_for_visit' => 'Regular checkup',
                'consultation_type' => 'in-person',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'appointment' => [
                    'id', 'patient_id', 'doctor_id', 'appointment_date', 'status'
                ]
            ]);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_patient_can_view_own_appointments()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $response = $this->actingAs($this->patient)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $appointment->id,
            ]);
    }

    public function test_patient_cannot_view_other_patient_appointments()
    {
        $otherPatient = User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::factory()->create([
            'patient_id' => $otherPatient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $response = $this->actingAs($this->patient)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonMissing([
                'id' => $appointment->id,
            ]);
    }

    public function test_doctor_can_view_their_appointments()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
        ]);

        $response = $this->actingAs($this->doctor)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $appointment->id,
            ]);
    }

    public function test_patient_can_cancel_appointment()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->patient)
            ->postJson("/api/appointments/{$appointment->id}/cancel", [
                'reason' => 'Personal reasons',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_unauthenticated_user_cannot_book_appointment()
    {
        $response = $this->postJson('/api/appointments', [
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'reason_for_visit' => 'Regular checkup',
        ]);

        $response->assertStatus(401);
    }
}
